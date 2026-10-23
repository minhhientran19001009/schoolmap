import assert from 'node:assert/strict';
import fs from 'node:fs/promises';
import path from 'node:path';
import vm from 'node:vm';
import {createRequire} from 'node:module';
import {fileURLToPath} from 'node:url';
import {webcrypto} from 'node:crypto';
const root=path.resolve(path.dirname(fileURLToPath(import.meta.url)),'..');
const install=path.join(root,'install'),source=path.join(root,'source');
const manifest=JSON.parse(await fs.readFile(path.join(install,'appsscript.json'),'utf8'));
const originalCatalog=JSON.parse(await fs.readFile(path.join(source,'data/training-schools.json'),'utf8'));
let tests=0;const checks=[];
function check(name,run){run();tests++;checks.push(name);}
async function checkAsync(name,run){await run();tests++;checks.push(name);}

// In-memory doubles implement Apps Script services; records are stored only in sheets.
const properties=new Map(),books=new Map();let active='owner@example.test',effective=active;
let uuid=0,bookCounter=0,held=false,busy=false,acquisitionHook=null,flushes=0;
class Sheet {
  constructor(name){this.name=name;this.rows=[];}
  getLastRow(){return this.rows.length;}
  getRange(row,col,n,m){const self=this;return {
    setNumberFormat(format){assert.equal(format,'@');return this;},
    setValues(values){assert.equal(values.length,n);values.forEach((cells,i)=>{assert.equal(cells.length,m);self.rows[row+i-1]||=[];cells.forEach((v,j)=>self.rows[row+i-1][col+j-1]=v);});return this;},
    getValues(){return Array.from({length:n},(_,i)=>Array.from({length:m},(_,j)=>self.rows[row+i-1]?.[col+j-1]??''));}
  };}
  setFrozenRows(){}setColumnWidth(){}
}
class Book {
  constructor(id){this.id=id;this.sheets=new Map();}
  getId(){return this.id;}getUrl(){return 'https://docs.google.com/spreadsheets/d/'+this.id;}
  getSheetByName(n){return this.sheets.get(n);}insertSheet(n){let s=new Sheet(n);this.sheets.set(n,s);return s;}
}
const logs=[];
const context={console:{log:(x)=>logs.push(x),error:()=>{}},
  PropertiesService:{getScriptProperties:()=>({getProperty:k=>properties.get(k)||null,setProperties:o=>Object.entries(o).forEach(([k,v])=>properties.set(k,v))})},
  Session:{getActiveUser:()=>({getEmail:()=>active}),getEffectiveUser:()=>({getEmail:()=>effective})},
  SpreadsheetApp:{create:()=>{const b=new Book('sheet-'+(++bookCounter));books.set(b.id,b);return b;},openById:id=>{if(!books.has(id))throw Error('No spreadsheet');return books.get(id);},flush:()=>{assert(held,'flush must occur before releasing write lock');flushes++;}},
  LockService:{getScriptLock:()=>({waitLock(){if(busy)throw Error('Lock timeout');held=true;},tryLock(){if(busy)return false;held=true;if(acquisitionHook){const hook=acquisitionHook;acquisitionHook=null;hook();}return true;},releaseLock(){assert(held);held=false;}})},
  Utilities:{getUuid:()=>`record-${++uuid}`},
  HtmlService:{XFrameOptionsMode:{ALLOWALL:'ALLOWALL'},createHtmlOutputFromFile(name){return output({file:name});},createHtmlOutput(content){return output({content});}}
};
function output(o){return Object.assign(o,{setTitle(){return this;},addMetaTag(){return this;},setXFrameOptionsMode(m){this.frameMode=m;return this;}});}
vm.createContext(context);
for(const file of ['Rules.gs','Data.gs','Code.gs'])vm.runInContext(await fs.readFile(path.join(install,file),'utf8'),context,{filename:file});
const rpc=(path,method='GET',body)=>context.portalRequest({path,method,...(body?{body}: {})});
const post=(action,record)=>rpc('/api/portal','POST',{action,record});
const sample=context.PortalRules.SEED;
const base=()=>JSON.parse(JSON.stringify(sample.listings[0]));
check('No access or data before owner setup',()=>{assert.equal(rpc('/api/portal').status,403);assert(!context.doGet().file);});
check('Setup creates private data store with source catalogs and safe samples',()=>{
  const r=context.setupPortal();assert.match(r.spreadsheetUrl,/sheet-1/);assert.equal(bookCounter,1);assert.equal(properties.get('PORTAL_OWNER_EMAIL'),active);
  assert.equal(rpc('/api/employers').body.employers.length,24);const s=rpc('/api/training-schools').body;assert.equal(s.schools.length,14);assert.equal(s.schools.reduce((n,s)=>n+s.programs.length,0),195);
  assert.equal(rpc('/api/portal').body.listings.length,15);assert.equal(rpc('/api/portal').body.proposals.length,2);assert.equal(context.doGet().file,'Index');
});
check('Repeated setup preserves rows and does not create another spreadsheet',()=>{context.setupPortal();assert.equal(bookCounter,1);assert.equal(rpc('/api/portal').body.listings.length,15);});
check('Blank visitor identity cannot inherit deployment owner privileges',()=>{active='';assert.equal(rpc('/api/portal').status,403);assert.equal(rpc('/api/employers').status,403);assert.equal(post('listing',base()).status,403);assert(!context.doGet().file);assert.throws(()=>context.setupPortal());active=effective;});
check('Unapproved signed-in account cannot read or write',()=>{active='outsider@example.test';assert.equal(rpc('/api/training-schools').status,403);assert.equal(post('listing',base()).status,403);assert.throws(()=>context.setupPortal());active=effective;});
check('Invalid types, missing fields, quantity limits and unknown occupations are rejected',()=>{
  assert.equal(context.portalRequest(null).status,400);assert.equal(post('listing',{}).status,400);
  for(const quantity of [0,-1,1.5,100001,'50'])assert.equal(post('listing',{...base(),quantity}).status,400);
  assert.equal(post('listing',{...base(),profession:'Nghề chưa xác nhận'}).status,400);assert.equal(post('listing',{...base(),skills:[]}).status,400);
  assert.equal(post('listing',{...base(),note:'x'.repeat(25000)}).status,413);assert.equal(post('proposal',{demandId:'d1',capacityId:'c1',quantity:1,plan:'ngắn'}).status,400);
});
let demandId,capacityId;
check('Create demand and capacity; reload reads persisted Sheets rows',()=>{
  let r=post('listing',{...base(),org:'  Doanh nghiệp kiểm thử  ',quantity:30});assert.equal(r.status,201);demandId=r.body.id;
  r=post('listing',{...base(),kind:'capacity',org:'Cơ sở kiểm thử',quantity:25});assert.equal(r.status,201);capacityId=r.body.id;
  const rows=rpc('/api/portal').body.listings;assert.equal(rows.length,17);let d=rows.find(x=>x.id===demandId);assert.equal(d.org,'Doanh nghiệp kiểm thử');assert.equal(d.sample,0);assert.match(d.createdAt,/Z$/);
  // New RPC context has no client data state: persistence still comes from Sheets.
  assert.equal(rpc('/api/portal').body.listings.find(x=>x.id===capacityId).quantity,25);
});
const plan='Bổ sung kỹ năng và thực hành tại doanh nghiệp trước khi đánh giá.';
check('Proposal reduces available quantities and survives reload',()=>{const r=post('proposal',{demandId,capacityId,quantity:20,plan});assert.equal(r.status,201);const data=rpc('/api/portal').body;assert.equal(context.PortalRules.remaining(data.listings.find(x=>x.id===capacityId),data.proposals),5);assert.equal(data.proposals.at(-1).sample,0);});
check('Reject mismatched occupation, level, period, missing IDs, reversed roles',()=>{
  for(const pair of [['d1','c2'],['d1','c7'],['d1','missing'],['c1','d1']])assert.equal(post('proposal',{demandId:pair[0],capacityId:pair[1],quantity:1,plan}).status,409);
  const c=post('listing',{...base(),kind:'capacity',level:'Trung cấp',quantity:10}).body.id;assert.equal(post('proposal',{demandId:'d1',capacityId:c,quantity:1,plan}).status,409);
});
check('Capacity limit and demand limit both apply on the server',()=>{
  assert.equal(post('proposal',{demandId,capacityId,quantity:6,plan}).status,409);
  const d=post('listing',{...base(),quantity:2}).body.id,c=post('listing',{...base(),kind:'capacity',quantity:10}).body.id;
  assert.equal(post('proposal',{demandId:d,capacityId:c,quantity:3,plan}).status,409);
});
check('Concurrent update is re-read after acquiring the shared lock',()=>{
  const book=books.get(properties.get('PORTAL_SPREADSHEET_ID'));
  acquisitionHook=()=>{assert(held);context.appendRecord_(book,'DeXuat',{id:'other-writer',demandId,capacityId,quantity:4,plan,sample:0,createdAt:'2026-10-01T00:00:00Z'});};
  assert.equal(post('proposal',{demandId,capacityId,quantity:2,plan}).status,409);
  assert.equal(post('proposal',{demandId,capacityId,quantity:1,plan}).status,201);
  assert.equal(post('proposal',{demandId,capacityId,quantity:1,plan}).status,409);assert.equal(held,false);
});
check('Busy lock produces retry response without saving',()=>{const before=rpc('/api/portal').body.listings.length;busy=true;assert.equal(post('listing',base()).status,503);busy=false;assert.equal(rpc('/api/portal').body.listings.length,before);});
check('User text remains text and spoofed IDs/sample flags are replaced',()=>{
  const r=post('listing',{...base(),org:'=HYPERLINK("https://example.test")',id:'d1',sample:1});assert.equal(r.status,201);assert.notEqual(r.body.id,'d1');
  const book=books.get(properties.get('PORTAL_SPREADSHEET_ID')),raw=book.getSheetByName('NhuCau_NangLuc').rows.at(-1)[1];assert(raw.startsWith('{'));assert.equal(JSON.parse(raw).sample,0);
});
check('Reports preserve quarter separation, skill gaps, searchable catalog, safe CSV',()=>{
  const R=context.PortalRules;
  const rows=R.summary(sample,R.emptyFilters);assert.equal(rows.reduce((s,r)=>s+r.demand,0),800);assert.equal(rows.reduce((s,r)=>s+r.capacity,0),600);assert.equal(rows.reduce((s,r)=>s+r.gap,0),230);
  assert.equal(R.filtered(sample,{...R.emptyFilters,period:'Quý I/2027'}).length,2);assert.deepEqual(Array.from(R.skillGap(sample.listings[0],sample.listings[8])),['Tiếng Trung cơ bản']);assert.match(R.csv(rows,'Quý IV/2026'),/Nhu cầu/);
});
check('Missing source sheet fails visibly instead of inventing real data',()=>{const book=books.get(properties.get('PORTAL_SPREADSHEET_ID')),sheet=book.sheets.get('Nguon');book.sheets.delete('Nguon');assert.equal(rpc('/api/training-schools').status,503);book.sheets.set('Nguon',sheet);});
check('Manifest defaults to owner-only deployment and contains no external connection scope',()=>{assert.equal(manifest.webapp.access,'MYSELF');assert.equal(manifest.webapp.executeAs,'USER_DEPLOYING');assert(!manifest.oauthScopes.some(s=>/external_request|drive/.test(s)));});

// Exercise the actual frontend RPC adapter, including the embedded review dialog.
const moduleRoot=process.env.PORTAL_NODE_MODULES||path.join(source,'node_modules');
const require=createRequire(path.join(moduleRoot,'../package.json'));
let esbuild;try{esbuild=require('esbuild');}catch{esbuild=createRequire(require.resolve('vite'))('esbuild');}
const bridge=await esbuild.build({absWorkingDir:source,entryPoints:['bridge.ts'],bundle:true,write:false,platform:'browser',format:'iife',globalName:'Bridge',target:'es2020'});
const catalogHelpers=await esbuild.build({absWorkingDir:source,stdin:{contents:`export {filterTrainingSchools,schoolCapacityPrefill} from './lib/training-schools'; export {filterEmployers,demandPrefill} from './lib/employers';`,resolveDir:source,loader:'ts'},nodePaths:[moduleRoot],bundle:true,write:false,platform:'neutral',format:'iife',globalName:'CatalogHelpers'});
vm.runInContext(catalogHelpers.outputFiles[0].text,context);
check('Company and school lookup, original codes and prefilled forms remain available',()=>{
  const h=context.CatalogHelpers,d=context.PORTAL_DATA_;
  assert(h.filterEmployers(d.employers,{query:'hyundai',sector:'all',region:'all'}).length>0);
  const school=d.schools[0],program=school.programs[0],prefill=h.schoolCapacityPrefill(school,program);assert.equal(prefill.org,school.name);assert.equal(prefill.profession,program.profession);
  assert(h.filterTrainingSchools(d.schools,{query:school.name,type:'all',level:'all',profession:'all'}).length>0);assert.equal(h.demandPrefill(d.employers[0]).org,d.employers[0].name);
  const codes=Array.from(d.schools.flatMap(s=>s.programs.map(p=>p.code)));assert(codes.every(code=>typeof code==='string'));assert.deepEqual(codes,originalCatalog.schools.flatMap(s=>s.programs.map(p=>p.code)));
});

function browser(preview){
  const events=new Map();let success,failure,calls=[];
  const document={activeElement:null,addEventListener:(n,f)=>events.set(n,f),removeEventListener:(n)=>events.delete(n)};
  class Element {
    constructor(tag){this.tag=tag;this.children=[];this.style={};this.events={};this.value='';this.disabled=false;this.textContent='';}
    setAttribute(n,v){this[n]=v;}
    append(...children){for(const child of children){this.children.push(child);child.parent=this;}}
    remove(){if(this.parent)this.parent.children=this.parent.children.filter(x=>x!==this);}
    focus(){document.activeElement=this;}
    addEventListener(n,f){this.events[n]=f;}
    fire(n){this.events[n]?.({});}
  }
  document.body=new Element('body');const host=new Element('dialog-content');document.body.append(host);
  document.createElement=tag=>new Element(tag);document.querySelector=()=>host;
  const window={fetch:async()=>new Response('unrelated',{status:200}),PORTAL_PREVIEW_DATA:preview,
    google:{script:{run:{withSuccessHandler(f){success=f;return this;},withFailureHandler(f){failure=f;return this;},portalRequest(r){calls.push(r);success({status:200,body:{ok:true,request:r}});}}}}};
  const b={window,document,crypto:webcrypto,Response,Request,URL,Uint8Array,console};vm.createContext(b);vm.runInContext(bridge.outputFiles[0].text,b);b.Bridge.installBridge();
  return {window,document,host,calls,failure:()=>failure?.(new Error('Network unavailable'))};
}
function allElements(node){return [node,...node.children.flatMap(allElements)];}
await checkAsync('Frontend reads all three endpoints through google.script.run',async()=>{
  const b=browser();for(const path of ['/api/portal','/api/employers','/api/training-schools']){const r=await b.window.fetch(path);assert.equal(r.status,200);assert.equal((await r.json()).request.path,path);}assert.equal(b.calls.length,3);
  assert.equal(await (await b.window.fetch('https://example.test')).text(),'unrelated');assert.equal(b.calls.length,3);
});
await checkAsync('Embedded save requires typed review code before any RPC write',async()=>{
  const b=browser(),body={action:'listing',record:{...base(),org:'<script>literal text</script>'}};
  const promise=b.window.fetch('/api/portal',{method:'POST',body:JSON.stringify(body)});
  assert.equal(b.calls.length,0);const nodes=allElements(b.host),input=nodes.find(n=>n.tag==='input'),save=nodes.find(n=>n.tag==='button'&&n.textContent==='Xác nhận lưu'),label=nodes.find(n=>n.tag==='label');
  assert(save.disabled);assert(nodes.some(n=>n.textContent.includes('<script>literal text</script>')));assert.equal(input.parent.parent.parent,b.host);
  input.value='XXXX';input.fire('input');const code=label.textContent.match(/Nhập mã ([A-Z2-9]{4})/)[1];assert.equal(save.disabled,input.value!==code);
  input.value=code;input.fire('input');assert.equal(save.disabled,false);save.fire('click');const r=await promise;assert.equal(r.status,200);assert.equal(b.calls.length,1);assert.equal(b.calls[0].body.record.org,body.record.org);assert.equal(b.host.children.length,0);
});
await checkAsync('Cancelling embedded save keeps data out of the backend',async()=>{
  const b=browser(),p=b.window.fetch('/api/portal',{method:'POST',body:JSON.stringify({action:'listing',record:base()})});allElements(b.host).find(n=>n.textContent==='Quay lại').fire('click');assert.equal((await p).status,409);assert.equal(b.calls.length,0);
});
await checkAsync('Offline preview remains read-only and clearly rejects save',async()=>{
  const b=browser({'/api/portal':{listings:[],proposals:[]}});assert.equal((await b.window.fetch('/api/portal')).status,200);const r=await b.window.fetch('/api/portal',{method:'POST',body:'{}'});assert.equal(r.status,403);assert.match((await r.json()).error,/không lưu dữ liệu/);assert.equal(b.calls.length,0);
});
await checkAsync('Missing Apps Script connection and malformed JSON fail with visible errors',async()=>{
  const b=browser();delete b.window.google;assert.equal((await b.window.fetch('/api/portal')).status,503);assert.equal((await b.window.fetch('/api/portal',{method:'POST',body:'bad JSON'})).status,400);
});
await checkAsync('Apps Script communication failure preserves a retryable response',async()=>{
  const b=browser();b.window.google.script.run.portalRequest=()=>b.failure();const r=await b.window.fetch('/api/portal');assert.equal(r.status,503);assert.match((await r.json()).error,/thử lại/);
});
check('Index and preview are standalone, have correct heading and valid inline JavaScript',()=>{});
for(const filename of [path.join(install,'Index.html'),path.join(root,'XEM_THU.html')]){
  const html=await fs.readFile(filename,'utf8');assert(html.includes('KẾT NỐI CUNG CẦU'));assert(!/<script[^>]+src=/i.test(html));assert(!/<link[^>]+stylesheet/i.test(html));
  for(const match of html.matchAll(/<script>([\s\S]*?)<\/script>/g))new vm.Script(match[1]);
}
assert(flushes>0);assert.equal(held,false);
const report={status:'passed',checks:tests,scope:'Local behavioral tests using Apps Script service doubles and actual compiled frontend adapter. Google deployment and in-browser Google Sites embedding require installation by the owner.',results:checks};
await fs.writeFile(path.join(root,'KIEM_THU.json'),JSON.stringify(report,null,2)+'\n');
console.log(JSON.stringify(report,null,2));
