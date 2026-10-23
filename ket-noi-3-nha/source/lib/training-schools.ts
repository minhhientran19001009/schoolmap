import catalog from '../data/training-schools.json' with {type:'json'};

export type TrainingLevel = 'Cao đẳng'|'Trung cấp'|'Sơ cấp';
export type ReportedCounts = {total:number|null;college:number|null;intermediate:number|null;elementary:number|null};
export type TrainingProgram = {id:string;name:string;code:string;sourceRow:number;levels:TrainingLevel[];reported:ReportedCounts;rawCounts:(number|string|null)[];profession:string};
export type TrainingSchool = {id:string;name:string;code:string;type:'Cao đẳng'|'Trung cấp';sourceRow:number;reported:ReportedCounts;rawCounts:(number|string|null)[];groups:{label:string;level:TrainingLevel;sourceRow:number;reported:ReportedCounts;rawCounts:(number|string|null)[]}[];programs:TrainingProgram[];issues:string[]};
export type TrainingSource = {filename:string;sheet:string;title:string;periodLabel:string;importedAt:string;sha256:string;notes:string[]};
export type ListingPrefill = {org:string;district?:string;profession?:string;level?:string};
export const TRAINING_SOURCE:TrainingSource=catalog.source;
export const SCHOOL_CATALOG_REVISION='2026-10-01-v1-'+TRAINING_SOURCE.sha256.slice(0,16);
export const TRAINING_LEVELS=['Cao đẳng','Trung cấp','Sơ cấp'] as const;
export const BASE_PROFESSIONS=['Cơ điện tử','Điện công nghiệp','Cắt gọt kim loại','May công nghiệp','Nghiệp vụ nhà hàng','Tin học ứng dụng'] as const;
export function normalizeTraining(value:string){return value.normalize('NFD').replace(/[\u0300-\u036f]/g,'').replace(/đ/g,'d').replace(/Đ/g,'D').toLowerCase().trim();}
// Keep original names/codes in the source record. Reuse a consistent selection label only
// for variants in case, accents, spaces and punctuation; do not infer equivalent curricula.
const nameKey=(value:string)=>normalizeTraining(value).replace(/[\s,.-]+/g,'');
const professionNames=new Map<string,string>();
for(const name of [...BASE_PROFESSIONS,...catalog.schools.flatMap(s=>s.programs.map(p=>p.name))])if(!professionNames.has(nameKey(name)))professionNames.set(nameKey(name),name);
export const ALL_PROFESSIONS=[...professionNames.values()] as [string,...string[]];
export const TRAINING_PROFESSIONS=[...new Set(catalog.schools.flatMap(s=>s.programs.map(p=>professionNames.get(nameKey(p.name))!)))].sort((a,b)=>a.localeCompare(b,'vi'));
export function canonicalProfession(name:string){return professionNames.get(nameKey(name))??name;}
export const TRAINING_SCHOOLS:TrainingSchool[]=catalog.schools.map(s=>({...s,programs:s.programs.map(p=>({...p,profession:canonicalProfession(p.name)}))})) as TrainingSchool[];
export type SchoolFilters={query:string;type:string;level:string;profession:string};
export const emptySchoolFilters:SchoolFilters={query:'',type:'all',level:'all',profession:'all'};
export function filterTrainingSchools(records:TrainingSchool[],filters:SchoolFilters){
  const searchable=(value:string)=>normalizeTraining(value).replace(/\s+/g,'');
  const q=searchable(filters.query);
  return records.flatMap(s=>{
    if(filters.type!=='all'&&s.type!==filters.type)return [];
    const expanded=normalizeTraining(s.name).replace(/\bcd\b/g,'cao dang').replace(/\bnd\b/g,'nam dinh').replace(/\bgtvt\b/g,'giao thong van tai').replace(/\bvhnt\b/g,'van hoa nghe thuat');
    const schoolMatches=!!q&&searchable([s.name,s.code,expanded].join(' ')).includes(q);
    const programs=s.programs.filter(p=>(filters.level==='all'||(filters.level==='unknown'?!p.levels.length:p.levels.includes(filters.level as TrainingLevel)))&&(filters.profession==='all'||p.profession===filters.profession)&&(!q||schoolMatches||searchable([p.name,p.profession,p.code,...p.levels].join(' ')).includes(q)));
    return programs.length?[{school:s,programs}]:[];
  });
}
export function schoolCapacityPrefill(school:TrainingSchool,program?:TrainingProgram):ListingPrefill {
  return {org:school.name,...(program?{profession:program.profession,...(program.levels.length?{level:program.levels[0]}:{})}:{})};
}
