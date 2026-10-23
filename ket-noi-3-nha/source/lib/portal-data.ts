import { z } from 'zod';
import {ALL_PROFESSIONS} from './training-schools';
export const PROFESSIONS = ALL_PROFESSIONS;
export const LEVELS = ['Sơ cấp','Trung cấp','Cao đẳng','Dưới 3 tháng'] as const;
export const PERIODS = ['Quý IV/2026','Quý I/2027'] as const;
export const PURPOSES = ['Tuyển dụng','Đào tạo mới','Đào tạo lại'] as const;
export const listingSchema = z.object({
  kind:z.enum(['demand','capacity']), org:z.string().trim().min(3).max(160),
  profession:z.enum(PROFESSIONS), level:z.enum(LEVELS), period:z.enum(PERIODS),
  quantity:z.number().int().min(1).max(100000), skills:z.array(z.string().trim().min(1).max(80)).min(1).max(20),
  district:z.string().trim().min(2).max(120), purpose:z.enum(PURPOSES).default('Tuyển dụng'),
  age:z.string().trim().max(80).default(''), salary:z.string().trim().max(100).default(''),
  duration:z.string().trim().max(100).default(''), equipment:z.string().trim().max(1000).default(''),
  note:z.string().trim().max(2000).default(''),
});
export const proposalSchema = z.object({demandId:z.string().min(1).max(100),capacityId:z.string().min(1).max(100),quantity:z.number().int().min(1).max(100000),plan:z.string().trim().min(10).max(2000)});
export type Listing = z.infer<typeof listingSchema> & {id:string;sample:number;createdAt:string};
export type Proposal = z.infer<typeof proposalSchema> & {id:string;sample:number;createdAt:string};
export type Data = {listings:Listing[];proposals:Proposal[]};
export type Filters = {profession:string;level:string;period:string;query:string};
export const emptyFilters:Filters={profession:'all',level:'all',period:PERIODS[0],query:''};
export function normalize(v:string){return v.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/g,'d').replace(/Đ/g,'D').toLowerCase();}
export function filtered(data:Data,f:Filters) {return data.listings.filter(r=>(f.profession==='all'||r.profession===f.profession)&&(f.level==='all'||r.level===f.level)&&r.period===f.period&&(!f.query||normalize([r.org,r.profession,r.district,...r.skills].join(' ')).includes(normalize(f.query))));}
export function matches(d:Listing,c:Listing){return d.kind==='demand'&&c.kind==='capacity'&&d.profession===c.profession&&d.level===c.level&&d.period===c.period;}
export function remaining(r:Listing,proposals:Proposal[]){return Math.max(0,r.quantity-proposals.filter(p=>r.kind==='demand'?p.demandId===r.id:p.capacityId===r.id).reduce((s,p)=>s+p.quantity,0));}
export function skillGap(d:Listing,c:Listing){return d.skills.filter(s=>!c.skills.some(k=>normalize(k)===normalize(s)));}
export function summary(data:Data,f:Filters){
  const scope=filtered(data,{...f,query:''});
  const keys=new Set(filtered(data,f).map(r=>r.profession+'|'+r.level));
  const groups=[...new Set(scope.map(r=>r.profession+'|'+r.level))].filter(k=>keys.has(k));
  return groups.map(key=>{const rows=scope.filter(r=>r.profession+'|'+r.level===key), d=rows.filter(r=>r.kind==='demand'),c=rows.filter(r=>r.kind==='capacity');const demand=d.reduce((s,r)=>s+r.quantity,0),capacity=c.reduce((s,r)=>s+r.quantity,0);const ids=new Set(d.map(r=>r.id));return {key,profession:rows[0].profession,level:rows[0].level,demand,capacity,gap:Math.max(0,demand-capacity),surplus:Math.max(0,capacity-demand),proposed:data.proposals.filter(p=>ids.has(p.demandId)).reduce((s,p)=>s+p.quantity,0),companies:new Set(d.map(r=>r.org)).size,schools:new Set(c.map(r=>r.org)).size};}).sort((a,b)=>b.gap-a.gap||b.demand-a.demand);
}
export function csv(rows:ReturnType<typeof summary>,period:string){const cell=(v:unknown)=>{let s=String(v);if(/^[=+@\-\t\r]/.test(s))s="'"+s;return '"'+s.replace(/"/g,'""')+'"';};return '\uFEFF'+[['Kỳ','Nghề','Trình độ','Nhu cầu (người)','Năng lực dự kiến (người)','Chênh lệch thiếu (người)','Dư năng lực (người)','Đã đề xuất (người)','Số doanh nghiệp','Số cơ sở'],...rows.map(r=>[period,r.profession,r.level,r.demand,r.capacity,r.gap,r.surplus,r.proposed,r.companies,r.schools])].map(row=>row.map(cell).join(',')).join('\r\n');}
const base={period:PERIODS[0],purpose:'Tuyển dụng' as const,age:'Từ 18 tuổi',salary:'Thỏa thuận (minh họa)',duration:'',equipment:'',note:'Dữ liệu giả định để thử cổng; không phải thông báo tuyển dụng hoặc tuyển sinh.',sample:1,createdAt:'2026-10-01T02:00:00.000Z'};
function demo(id:string,kind:'demand'|'capacity',org:string,profession:typeof PROFESSIONS[number],level:typeof LEVELS[number],quantity:number,skills:string[],district:string,extra:Partial<Listing>={}):Listing{return {...base,id,kind,org,profession,level,quantity,skills,district,...extra};}
export const SEED:Data={listings:[
  demo('d1','demand','Doanh nghiệp Điện tử A (mẫu)','Cơ điện tử','Cao đẳng',180,['PLC','Lắp ráp','Tiếng Trung cơ bản'],'Khu vực Hà Nam'),
  demo('d2','demand','Doanh nghiệp Cơ khí B (mẫu)','Điện công nghiệp','Trung cấp',120,['An toàn điện','Bảo trì','Đọc bản vẽ'],'Khu vực Ninh Bình'),
  demo('d3','demand','Doanh nghiệp Cơ khí B (mẫu)','Cắt gọt kim loại','Trung cấp',80,['CNC','Đọc bản vẽ','Đo kiểm'],'Khu vực Ninh Bình'),
  demo('d4','demand','Doanh nghiệp Dệt may C (mẫu)','May công nghiệp','Sơ cấp',240,['May công nghiệp','Kiểm tra chất lượng'],'Khu vực Nam Định'),
  demo('d5','demand','Doanh nghiệp Du lịch D (mẫu)','Nghiệp vụ nhà hàng','Sơ cấp',60,['Phục vụ bàn','Tiếng Anh giao tiếp'],'Khu vực Ninh Bình'),
  demo('d6','demand','Doanh nghiệp Dịch vụ E (mẫu)','Tin học ứng dụng','Dưới 3 tháng',50,['Excel','Kỹ năng số'],'Khu vực Nam Định',{purpose:'Đào tạo lại'}),
  demo('d7','demand','Doanh nghiệp Điện tử A (mẫu)','Điện công nghiệp','Trung cấp',70,['Bảo trì','An toàn điện'],'Khu vực Hà Nam',{purpose:'Đào tạo mới'}),
  demo('d8','demand','Doanh nghiệp Điện tử F (mẫu)','Cơ điện tử','Cao đẳng',100,['PLC','Robot'],'Khu vực Hà Nam',{period:PERIODS[1]}),
  demo('c1','capacity','Cơ sở GDNN A (mẫu)','Cơ điện tử','Cao đẳng',100,['PLC','Lắp ráp'],'Khu vực Hà Nam',{duration:'Bổ sung kỹ năng: 8 tuần',equipment:'Xưởng cơ điện tử, mô hình PLC; 6 giảng viên'}),
  demo('c2','capacity','Cơ sở GDNN B (mẫu)','Điện công nghiệp','Trung cấp',130,['An toàn điện','Bảo trì','Đọc bản vẽ'],'Khu vực Ninh Bình',{duration:'Bổ sung kỹ năng: 6 tuần',equipment:'Xưởng điện công nghiệp; 8 giảng viên'}),
  demo('c3','capacity','Cơ sở GDNN B (mẫu)','Cắt gọt kim loại','Trung cấp',50,['CNC','Đọc bản vẽ','Đo kiểm'],'Khu vực Ninh Bình',{duration:'Bổ sung kỹ năng: 10 tuần',equipment:'4 máy CNC, phòng đo kiểm; 5 giảng viên'}),
  demo('c4','capacity','Cơ sở GDNN C (mẫu)','May công nghiệp','Sơ cấp',180,['May công nghiệp','Kiểm tra chất lượng'],'Khu vực Nam Định',{duration:'Khóa sơ cấp: 3 tháng',equipment:'60 máy may công nghiệp; 6 giảng viên'}),
  demo('c5','capacity','Cơ sở GDNN D (mẫu)','Nghiệp vụ nhà hàng','Sơ cấp',80,['Phục vụ bàn','Tiếng Anh giao tiếp'],'Khu vực Ninh Bình',{duration:'Khóa sơ cấp: 3 tháng',equipment:'Phòng thực hành nhà hàng; 4 giảng viên'}),
  demo('c6','capacity','Cơ sở GDNN C (mẫu)','Tin học ứng dụng','Dưới 3 tháng',60,['Excel','Kỹ năng số'],'Khu vực Nam Định',{duration:'Khóa kỹ năng: 4 tuần',equipment:'2 phòng máy tính; 4 giảng viên'}),
  demo('c7','capacity','Cơ sở GDNN A (mẫu)','Cơ điện tử','Cao đẳng',120,['PLC','Robot'],'Khu vực Hà Nam',{period:PERIODS[1],duration:'Bổ sung kỹ năng: 8 tuần',equipment:'Phòng thực hành robot; 6 giảng viên'}),
],proposals:[{id:'p1',demandId:'d2',capacityId:'c2',quantity:80,plan:'Phối hợp khảo sát kỹ năng; bổ sung an toàn điện và bảo trì trước khi đánh giá tại doanh nghiệp.',sample:1,createdAt:base.createdAt},{id:'p2',demandId:'d4',capacityId:'c4',quantity:100,plan:'Tổ chức lớp theo đơn đặt hàng, thực hành may và kiểm tra chất lượng tại xưởng.',sample:1,createdAt:base.createdAt}]};
