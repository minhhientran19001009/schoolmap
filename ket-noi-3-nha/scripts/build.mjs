import fs from 'node:fs/promises';
import path from 'node:path';
import {createRequire} from 'node:module';
import vm from 'node:vm';
import {fileURLToPath} from 'node:url';

const root=path.resolve(path.dirname(fileURLToPath(import.meta.url)),'..');
const source=path.join(root,'source'),install=path.join(root,'install');
const moduleRoot=process.env.PORTAL_NODE_MODULES||path.join(source,'node_modules');
const require=createRequire(path.join(moduleRoot,'../package.json'));
let esbuild;
try{esbuild=require('esbuild');}catch{esbuild=createRequire(require.resolve('vite'))('esbuild');}
const common={absWorkingDir:source,bundle:true,write:false,tsconfig:path.join(source,'tsconfig.json'),nodePaths:[moduleRoot],target:'es2020',format:'iife',legalComments:'eof',metafile:true};
await fs.mkdir(install,{recursive:true});
const [ui,rules,data]=await Promise.all([
  esbuild.build({...common,entryPoints:['entry.tsx'],minify:true,platform:'browser',define:{'process.env.NODE_ENV':'"production"'}}),
  esbuild.build({...common,entryPoints:['rules-entry.ts'],globalName:'PortalRules',platform:'neutral'}),
  esbuild.build({...common,entryPoints:['data-entry.ts'],globalName:'CatalogExport',platform:'neutral'})
]);
const sandbox={};vm.createContext(sandbox);vm.runInContext(data.outputFiles[0].text,sandbox);
const c=sandbox.CatalogExport;
const catalog={employers:c.employers,reviewedAt:c.reviewedAt,schools:c.schools,source:c.source,
  exportInfo:{id:'google-sites-2026-10-01',title:'Bộ mã chuyển từ Cổng kết nối 3 nhà Ninh Bình',sourceCommit:'f5323aa0696288007ca14d89b723b32377231fc4',exportedAt:'2026-10-01',initialization:'Danh mục có nguồn và bản ghi minh họa; không xuất dữ liệu phát sinh từ cơ sở dữ liệu đang chạy.'}};
await fs.writeFile(path.join(install,'Rules.gs'),rules.outputFiles[0].text);
await fs.writeFile(path.join(install,'Data.gs'),'// Danh mục có nguồn từ phiên bản cổng 01/10/2026; giữ nguyên tên, mã và ghi chú.\nvar PORTAL_DATA_ = '+JSON.stringify(catalog,null,2)+';\n');
const css=await fs.readFile(path.join(source,'portal.css'),'utf8');
const reviewCss=`.save-review{position:fixed;inset:0;z-index:2147483000;display:flex;align-items:center;justify-content:center;padding:20px;background:rgba(17,37,64,.78);pointer-events:auto;overflow:auto}.save-review.in-form{position:absolute;padding:15px;border-radius:10px}.save-review section{width:100%;max-width:540px;max-height:100%;overflow:auto;background:white;color:#192c46;border-radius:10px;padding:24px;box-shadow:0 20px 80px #071a3940}.save-review h2{font-size:21px;margin-bottom:12px}.save-review p{font-size:14px;line-height:1.7;overflow-wrap:anywhere;margin-bottom:15px}.save-review label{display:block;font-size:14px;font-weight:600;margin:12px 0 8px}.save-review input{display:block;width:100%;min-height:44px;border:1px solid #91a6c0;border-radius:6px;padding:10px;font-size:19px;letter-spacing:.15em;text-transform:uppercase}.save-review section>div{display:flex;justify-content:flex-end;gap:10px;margin-top:18px}.save-review button{border:1px solid #b9c9dc;border-radius:6px;background:#fff;color:#0b3e82;padding:10px 16px;font-size:14px}.save-review button:last-child{background:#0b3e82;color:#fff}.save-review button:disabled{opacity:.45;cursor:not-allowed}@media(max-width:520px){.save-review section{padding:18px}.save-review.in-form{padding:8px}}`;
const safeScript=s=>s.replace(/<\/script/gi,'<\\/script');
const html=bootstrap=>`<!doctype html>\n<html lang="vi"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><base target="_blank"><title>KẾT NỐI CUNG CẦU | Cổng 3 nhà Ninh Bình</title><style>${css}\n${reviewCss}</style></head><body><div id="root"></div><noscript>Bật JavaScript để sử dụng cổng kết nối 3 nhà.</noscript>${bootstrap}<script>${safeScript(ui.outputFiles[0].text)}</script></body></html>\n`;
await fs.writeFile(path.join(install,'Index.html'),html(''));
const previewContext={};vm.createContext(previewContext);vm.runInContext(rules.outputFiles[0].text,previewContext);
const previewData={'/api/portal':previewContext.PortalRules.SEED,'/api/employers':{employers:catalog.employers,reviewedAt:catalog.reviewedAt},'/api/training-schools':{schools:catalog.schools,source:catalog.source}};
await fs.writeFile(path.join(root,'XEM_THU.html'),html(`<div style="padding:12px 24px;background:#fff2d7;color:#724800;font:14px Arial"><strong>BẢN XEM THỬ NGOẠI TUYẾN</strong> · Tra cứu và lọc dữ liệu; chức năng ghi dữ liệu cần cài Apps Script.</div><script>window.PORTAL_PREVIEW_DATA=${safeScript(JSON.stringify(previewData))};</script>`));
await fs.writeFile(path.join(root,'source/catalog-export.json'),JSON.stringify(catalog,null,2)+'\n');
// Include license text for dependencies actually bundled into the two runtimes.
const packages=new Map();
for(const result of [ui,rules])for(const input of Object.keys(result.metafile.inputs)){
  let dir=path.dirname(path.resolve(source,input));
  while(dir!==path.dirname(dir)){
    try{const pkg=JSON.parse(await fs.readFile(path.join(dir,'package.json'),'utf8'));if(dir.includes('node_modules'))packages.set(dir,pkg);break;}catch{}
    dir=path.dirname(dir);
  }
}
let notices='# Third-party notices\n\nGenerated application code includes the following open-source packages.\n';
for(const [dir,pkg] of packages){notices+=`\n## ${pkg.name} ${pkg.version}\nLicense: ${pkg.license||'See package license'}\n`;
  for(const file of await fs.readdir(dir))if(/^(licen[sc]e|copying|notice)(\.|$)/i.test(file))try{notices+='\n'+await fs.readFile(path.join(dir,file),'utf8')+'\n';}catch{}
}
await fs.writeFile(path.join(root,'THIRD_PARTY_NOTICES.txt'),notices);
await fs.writeFile(path.join(root,'scripts/metafile.json'),JSON.stringify(ui.metafile,null,2));
console.log(JSON.stringify({status:'built',employers:catalog.employers.length,schools:catalog.schools.length,programRows:catalog.schools.reduce((n,s)=>n+s.programs.length,0),browserBytes:ui.outputFiles[0].text.length,files:['Code.gs','Rules.gs','Data.gs','Index.html','appsscript.json']}));
