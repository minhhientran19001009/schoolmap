'use client';
import {useState} from 'react';
import {Building2, MapPin, Search, RefreshCw, Plus, FileText, Phone, Info} from 'lucide-react';
import {Button} from '@/components/ui/button';
import {Input} from '@/components/ui/input';
import {Label} from '@/components/ui/label';
import {Select, SelectTrigger, SelectValue, SelectContent, SelectItem} from '@/components/ui/select';
import {Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription} from '@/components/ui/sheet';
import {Empty, EmptyHeader, EmptyTitle, EmptyDescription} from '@/components/ui/empty';
import {Skeleton} from '@/components/ui/skeleton';
import {SECTORS, REGIONS, emptyEmployerFilters, filterEmployers, type Employer, type EmployerFilters} from '@/lib/employers';

const nf=new Intl.NumberFormat('vi-VN');
function Filter({label,value,options,onChange}:{label:string;value:string;options:readonly string[];onChange:(value:string)=>void}) {
  return <div className="picker"><Label>{label}</Label><Select value={value} onValueChange={onChange}><SelectTrigger aria-label={label}><SelectValue/></SelectTrigger><SelectContent><SelectItem value="all">Tất cả</SelectItem>{options.map(o=><SelectItem key={o} value={o}>{o}</SelectItem>)}</SelectContent></Select></div>;
}
export default function CompanyDirectory({records,loading,error,onReload,onCreateDemand}:{records:Employer[];loading:boolean;error:string;onReload:()=>void;onCreateDemand:(employer:Employer)=>void}) {
  const [filters,setFilters]=useState<EmployerFilters>(emptyEmployerFilters),[detail,setDetail]=useState<Employer|null>(null);
  const results=filterEmployers(records,filters);
  const change=(key:keyof EmployerFilters,value:string)=>setFilters(f=>({...f,[key]:value}));
  const create=(e:Employer)=>{setDetail(null);onCreateDemand(e);};
  return <section className="company-directory" aria-label="Danh bạ doanh nghiệp Ninh Bình">
    <div className="section-heading"><div><h2>Doanh nghiệp lớn tại Ninh Bình</h2><p>{records.length} hồ sơ chọn lọc · Rà soát ngày 01/10/2026 · Địa bàn tỉnh sau sáp nhập</p></div><span className="tag sourced"><FileText size={14}/> Hồ sơ có nguồn</span></div>
    <div className="directory-note"><Info size={18}/><p>Số lao động tham gia BHXH tháng 7/2026 giúp tham khảo quy mô từng hồ sơ. <strong>Đây không phải số vị trí đang tuyển.</strong> Danh bạ chưa được tính vào báo cáo cung–cầu.</p></div>
    <div className="directory-filters"><div className="search-field"><Label htmlFor="company-search">Tìm doanh nghiệp</Label><div><Search size={18}/><Input id="company-search" value={filters.query} onChange={e=>change('query',e.target.value)} placeholder="Tên, Quanta, lĩnh vực, KCN…"/></div></div><Filter label="Lĩnh vực" value={filters.sector} options={SECTORS} onChange={v=>change('sector',v)}/><Filter label="Khu vực trước sáp nhập" value={filters.region} options={REGIONS} onChange={v=>change('region',v)}/><Button variant="ghost" onClick={()=>setFilters(emptyEmployerFilters)}>Đặt lại</Button></div>
    {error&&<div className="error-banner" role="alert"><span>{error} {records.length?'Đang hiển thị lần tải trước.':''}</span><Button variant="outline" onClick={onReload} disabled={loading}><RefreshCw size={16}/>Thử lại</Button></div>}
    {loading&&!records.length?<div className="loading-grid" aria-live="polite"><Skeleton className="h-60"/><Skeleton className="h-60"/><Skeleton className="h-60"/><p>Đang tải hồ sơ doanh nghiệp…</p></div>:<>
      <div className="directory-count" aria-live="polite">{results.length}/{records.length} hồ sơ phù hợp <span>· Sắp theo số lao động của hồ sơ BHXH</span></div>
      <div className="cards-grid company-grid">{results.map(e=><article key={e.id} className="panel company-card">
        <div className="company-card-top"><span className="company-icon"><Building2 size={21}/></span><span className="tag">{e.sector}</span></div>
        <h3>{e.name}</h3>{e.aliases.length>0&&<p className="company-alias">Tên tra cứu: {e.aliases.join(' · ')}</p>}
        <p className="company-location"><MapPin size={16}/><span>{e.location}</span></p>
        <div className="company-workforce"><strong>{nf.format(e.workers)}</strong><span>lao động BHXH · 07/2026</span></div>
        <p className="company-scope">{e.id==='song-hong'?'Hồ sơ công ty, không phải toàn hệ thống':e.id==='honda-ha-nam'?'Hồ sơ chi nhánh Hà Nam':'Một hồ sơ; không cộng các chi nhánh'}</p>
        {e.collaboration&&<span className="company-contact-tag"><Phone size={14}/> Có đầu mối công khai</span>}
        <div className="company-actions"><Button variant="outline" onClick={()=>setDetail(e)}>Xem hồ sơ & nguồn</Button><Button variant="ghost" onClick={()=>create(e)}><Plus size={16}/>Tạo nhu cầu</Button></div>
      </article>)}</div>
      {!results.length&&<Empty className="empty-state"><EmptyHeader><Search size={26}/><EmptyTitle>{error&&!records.length?'Chưa tải được danh bạ':'Không có doanh nghiệp phù hợp'}</EmptyTitle><EmptyDescription>{error&&!records.length?'Chọn Thử lại để tiếp tục.':'Thử tên khác hoặc đặt lại bộ lọc.'}</EmptyDescription></EmptyHeader></Empty>}
    </>}
    <p className="directory-footnote">Danh sách hỗ trợ lựa chọn đối tác Hội nghị 3 nhà, không phải bảng xếp hạng đầy đủ các doanh nghiệp lớn nhất tỉnh. Phạm vi hồ sơ BHXH có thể khác số lao động toàn nhà máy hoặc tập đoàn. Địa chỉ được đối chiếu nguồn; một số hồ sơ chỉ hiển thị KCN và khu vực khi chưa xác nhận đơn vị hành chính mới.</p>
    <Sheet open={!!detail} onOpenChange={open=>{if(!open)setDetail(null);}}><SheetContent className="detail-sheet company-sheet"><SheetHeader><SheetTitle>Hồ sơ doanh nghiệp</SheetTitle><SheetDescription>Thông tin có nguồn · Rà soát ngày 01/10/2026</SheetDescription></SheetHeader>{detail&&<div className="detail-body">
      <span className="tag sourced">{detail.sector}</span><h3>{detail.name}</h3>
      <dl><div><dt>Địa điểm</dt><dd>{detail.location}</dd></div><div><dt>Khu vực tra cứu</dt><dd>{detail.region}</dd></div><div><dt>Lao động BHXH</dt><dd><strong>{nf.format(detail.workers)} người</strong> · tháng 7/2026, trang {detail.workforcePage}</dd></div><div><dt>Phạm vi số liệu</dt><dd>{detail.scope}</dd></div><div><dt>Mã DN theo tài liệu BHXH</dt><dd>{detail.sourceCode}<small className="source-code-note">Chưa xác minh độc lập như mã số thuế.</small></dd></div><div><dt>Nhu cầu hiện tại</dt><dd>Chưa được doanh nghiệp xác nhận trong cổng.</dd></div></dl>
      {detail.note&&<><h4>Lưu ý đối chiếu</h4><p>{detail.note}</p></>}
      {detail.collaboration&&<section className="company-liaison"><h4>Đầu mối công khai</h4><p>{detail.collaboration.text}</p><strong>{detail.collaboration.contact}</strong><p>{detail.collaboration.phone}{detail.collaboration.email&&<><br/>{detail.collaboration.email}</>}</p><p className="liaison-note">Thông tin theo nguồn công bố; cần xác nhận đầu mối còn phụ trách trước khi liên hệ.</p></section>}
      <h4>Nguồn đối chiếu</h4><ol className="company-sources">{detail.sources.map((s,i)=><li key={i}>{s.url?<a href={s.url+(s.page&&s.url.endsWith('.pdf')?'#page='+s.page:'')} target="_blank" rel="noopener noreferrer">{s.title}</a>:<strong>{s.title}</strong>}<span>{s.publisher}{s.date?' · '+s.date:''}{s.page?' · Trang '+s.page:''}</span>{!s.url&&<small>Tài liệu “Danh sach doanh nghiep 072026.pdf” do người dùng cung cấp.</small>}</li>)}</ol>
      <div className="notice"><Info size={17}/><span>Chỉ điền sẵn tên và địa điểm. Đơn vị cần cung cấp nghề, kỹ năng, số lượng và kỳ nhu cầu trước khi lưu.</span></div><Button className="company-create" onClick={()=>create(detail)}><Plus size={17}/>Tạo nhu cầu cho doanh nghiệp</Button>
    </div>}</SheetContent></Sheet>
  </section>;
}
