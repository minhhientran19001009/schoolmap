'use client';
import {useState} from 'react';
import {GraduationCap, Search, Info, FileText, Plus, RefreshCw} from 'lucide-react';
import {Button} from '@/components/ui/button';
import {Input} from '@/components/ui/input';
import {Label} from '@/components/ui/label';
import {Select, SelectTrigger, SelectValue, SelectContent, SelectItem} from '@/components/ui/select';
import {Sheet, SheetContent, SheetHeader, SheetTitle, SheetDescription} from '@/components/ui/sheet';
import {Table, TableHeader, TableBody, TableRow, TableHead, TableCell} from '@/components/ui/table';
import {Empty, EmptyHeader, EmptyTitle, EmptyDescription} from '@/components/ui/empty';
import {Skeleton} from '@/components/ui/skeleton';
import {TRAINING_LEVELS, TRAINING_PROFESSIONS, emptySchoolFilters, filterTrainingSchools, type TrainingSchool, type TrainingProgram, type SchoolFilters, type TrainingSource} from '@/lib/training-schools';

const nf=new Intl.NumberFormat('vi-VN');
const display=(n:number|null)=>n===null?'—':nf.format(n);
function Picker({label,value,options,onChange}:{label:string;value:string;options:readonly {value:string;label:string}[];onChange:(value:string)=>void}){return <div className="picker"><Label>{label}</Label><Select value={value} onValueChange={onChange}><SelectTrigger aria-label={label}><SelectValue/></SelectTrigger><SelectContent><SelectItem value="all">Tất cả</SelectItem>{options.map(o=><SelectItem key={o.value} value={o.value}>{o.label}</SelectItem>)}</SelectContent></Select></div>;}
export default function TrainingDirectory({records,source,loading,error,onReload,onCreateCapacity}:{records:TrainingSchool[];source:TrainingSource|null;loading:boolean;error:string;onReload:()=>void;onCreateCapacity?:(school:TrainingSchool,program?:TrainingProgram)=>void}) {
  const [filters,setFilters]=useState<SchoolFilters>(emptySchoolFilters),[detail,setDetail]=useState<{school:TrainingSchool;programs:TrainingProgram[]}|null>(null);
  const results=filterTrainingSchools(records,filters),programCount=results.reduce((n,r)=>n+r.programs.length,0);
  const change=(key:keyof SchoolFilters,value:string)=>setFilters(f=>({...f,[key]:value}));
  function create(s:TrainingSchool,p?:TrainingProgram){setDetail(null);onCreateCapacity?.(s,p);}
  return <section className="training-directory" aria-label="Danh mục cơ sở GDNN và ngành nghề đào tạo">
    <div className="section-heading"><div><h2>Cơ sở GDNN & ngành, nghề đào tạo</h2><p>{records.length} cơ sở · {records.reduce((n,s)=>n+s.programs.length,0)} dòng ngành, nghề theo file cung cấp</p></div><span className="tag sourced"><FileText size={14}/> Danh mục từ bảng nguồn</span></div>
    <div className="directory-note"><Info size={18}/><div><p><strong>Kỳ số liệu cần xác nhận:</strong> tên file ghi 2026, nội dung bảng ghi 9 tháng đầu năm 2025. Một số ô tổng bị lỗi hoặc chưa khớp.</p><p className="training-period-note">Quy mô nguồn được giữ để tham khảo, chưa tính vào năng lực đáp ứng theo quý. Cơ sở cần khai báo riêng số lượng, kỹ năng và điều kiện có thể đáp ứng.</p></div></div>
    <div className="training-filters"><div className="search-field"><Label htmlFor="training-search">Tìm cơ sở hoặc nghề</Label><div><Search size={18}/><Input id="training-search" value={filters.query} onChange={e=>change('query',e.target.value)} placeholder="LILAMA, công nghệ ô tô, mã nghề…"/></div></div><Picker label="Loại cơ sở" value={filters.type} onChange={v=>change('type',v)} options={TRAINING_LEVELS.slice(0,2).map(s=>({value:s,label:s}))}/><Picker label="Trình độ trong dòng nguồn" value={filters.level} onChange={v=>change('level',v)} options={[...TRAINING_LEVELS.map(s=>({value:s,label:s})),{value:'unknown',label:'Chưa phân tách'}]}/><Picker label="Ngành, nghề" value={filters.profession} onChange={v=>change('profession',v)} options={TRAINING_PROFESSIONS.map(s=>({value:s,label:s}))}/><Button variant="ghost" onClick={()=>setFilters(emptySchoolFilters)}>Đặt lại</Button></div>
    {error&&<div className="error-banner" role="alert"><span>{error} {records.length?'Đang hiển thị lần tải trước.':''}</span><Button variant="outline" disabled={loading} onClick={onReload}><RefreshCw size={16}/>Thử lại</Button></div>}
    {loading&&!records.length?<div className="loading-grid" aria-live="polite"><Skeleton className="h-60"/><Skeleton className="h-60"/><Skeleton className="h-60"/><p>Đang tải cơ sở và ngành, nghề đào tạo…</p></div>:<>
      <div className="directory-count" aria-live="polite">{results.length}/{records.length} cơ sở phù hợp · {programCount} dòng ngành, nghề <span>· Không cộng trùng dòng tổng và dòng nghề</span></div>
      <div className="cards-grid training-grid">{results.map(({school:s,programs})=><article key={s.id} className="panel training-card">
        <div className="company-card-top"><span className="company-icon"><GraduationCap size={23}/></span><span className="tag">{s.type}</span></div><h3>{s.name}</h3><p className="training-code">{s.code?'Mã cơ sở theo file: '+s.code:'Chưa có mã cơ sở trong file'} · Dòng {s.sourceRow}</p>
        <div className="training-program-preview">{programs.slice(0,5).map(p=><p key={p.id}>{p.name}</p>)}{programs.length>5&&<span>Còn {programs.length-5} dòng ngành, nghề</span>}</div>
        <div className="training-raw-summary"><span>Quy mô Tổng theo dòng cơ sở</span><strong>{display(s.reported.total)}</strong><small>{s.reported.total===null?'Chưa có số tổng tại dòng cơ sở':'Theo bảng nguồn, chưa phải năng lực kỳ hiện tại'}</small></div>
        {s.issues.length>0&&<p className="training-issue-label">Có {s.issues.length} nội dung cần đối chiếu</p>}
        <div className="company-actions"><Button variant="outline" onClick={()=>setDetail({school:s,programs})}>Xem ngành, nghề</Button>{onCreateCapacity&&<Button variant="ghost" onClick={()=>create(s)}><Plus size={16}/>Khai báo năng lực</Button>}</div>
      </article>)}</div>
      {!results.length&&<Empty className="empty-state"><EmptyHeader><Search size={26}/><EmptyTitle>{error&&!records.length?'Chưa tải được danh mục':'Không có cơ sở hoặc nghề phù hợp'}</EmptyTitle><EmptyDescription>{error&&!records.length?'Chọn Thử lại để tiếp tục.':'Thử tên, mã nghề khác hoặc đặt lại bộ lọc.'}</EmptyDescription></EmptyHeader></Empty>}
    </>}
    <p className="directory-footnote">Tên và mã ngành, nghề được giữ theo nguồn. Nhãn chọn nghề gộp các khác biệt viết hoa, dấu và khoảng trắng; không tự suy ra các chương trình tương đương. Trình độ lấy từ cột có số liệu dương hoặc nhóm hệ đào tạo trong file; các dòng thiếu thông tin được ghi “Chưa phân tách”. Danh mục này chưa xác nhận tình trạng hoạt động, điều kiện hoặc phạm vi được cấp phép ở thời điểm hiện tại.</p>
    {source&&<p className="training-source-line">Nguồn: {source.filename} · Trang tính “{source.sheet}” · Nhập ngày 01/10/2026.</p>}
    <Sheet open={!!detail} onOpenChange={open=>{if(!open)setDetail(null);}}><SheetContent className="detail-sheet training-sheet"><SheetHeader><SheetTitle>{detail?.school.name}</SheetTitle><SheetDescription>Ngành, nghề theo bảng nguồn · Quy mô CĐ, TC, SC được giữ riêng</SheetDescription></SheetHeader>{detail&&<div className="detail-body">
      <div className="training-sheet-meta"><span className="tag sourced">{detail.school.type}</span><span>Mã: {detail.school.code||'Chưa có trong file'} · Dòng {detail.school.sourceRow}</span></div>
      <div className="training-summary-grid">{[['Tổng',detail.school.reported.total],['Cao đẳng',detail.school.reported.college],['Trung cấp',detail.school.reported.intermediate],['Sơ cấp',detail.school.reported.elementary]].map(([label,n])=><div key={String(label)}><span>{label}</span><strong>{display(n as number|null)}</strong></div>)}</div>
      <p className="training-table-note">Các số trên là ô nguồn tại dòng cơ sở. “—” là ô chưa có số liệu số; 0 được giữ nguyên. Không dùng số này như số người sẵn sàng cung ứng.</p>
      {detail.school.issues.length>0&&<div className="training-issues"><strong>Nội dung cần đối chiếu</strong><ul>{detail.school.issues.map(i=><li key={i}>{i}</li>)}</ul></div>}
      <h4>{detail.programs.length} dòng ngành, nghề trong bộ lọc</h4><Table className="training-program-table"><TableHeader><TableRow><TableHead>Ngành, nghề / mã theo file</TableHead><TableHead>Trình độ trong nguồn</TableHead><TableHead className="numeric">Tổng</TableHead><TableHead className="numeric">CĐ</TableHead><TableHead className="numeric">TC</TableHead><TableHead className="numeric">SC</TableHead>{onCreateCapacity&&<TableHead>Khai báo</TableHead>}</TableRow></TableHeader><TableBody>{detail.programs.map(p=><TableRow key={p.id}><TableCell><strong>{p.name}</strong><span className="training-program-code">{p.code||'Chưa có mã nghề'} · Dòng {p.sourceRow}</span></TableCell><TableCell>{p.levels.join(', ')||'Chưa phân tách'}</TableCell>{[p.reported.total,p.reported.college,p.reported.intermediate,p.reported.elementary].map((n,i)=><TableCell key={i} className="numeric">{display(n)}</TableCell>)}{onCreateCapacity&&<TableCell><Button variant="outline" size="sm" onClick={()=>create(detail.school,p)}>Chọn nghề</Button></TableCell>}</TableRow>)}</TableBody></Table>
      {detail.school.groups.length>0&&<><h4>Dòng tổng theo hệ trong nguồn</h4><div className="training-group-totals">{detail.school.groups.map(g=><p key={g.sourceRow}>{g.label} · Tổng {display(g.reported.total)} · Dòng {g.sourceRow}</p>)}</div><p className="training-table-note">Các dòng tổng theo hệ được giữ riêng, không tạo thành tên nghề và không cộng lại với các dòng chi tiết.</p></>}
      <h4>Nguồn đối chiếu</h4><p>{source?.filename} · Trang tính “{source?.sheet}” · Dòng cơ sở {detail.school.sourceRow}. Mỗi dòng nghề ghi vị trí nguồn bên cạnh mã.</p><p>{source?.title} {source?.periodLabel}</p>
      {onCreateCapacity&&<div className="notice"><Info size={17}/><span>Chọn nghề để điền sẵn cơ sở, nghề và trình độ có trong dòng nguồn. Nhập riêng số người có thể đáp ứng, kỳ, kỹ năng và địa điểm trước khi lưu.</span></div>}
    </div>}</SheetContent></Sheet>
  </section>;
}
