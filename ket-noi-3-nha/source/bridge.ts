// Map the existing portal API to Apps Script; no external API or CDN dependency.
declare global {
  interface Window { google?: any; PORTAL_PREVIEW_DATA?: any; }
}
const paths = new Set(['/api/portal','/api/employers','/api/training-schools']);
const originalFetch = window.fetch.bind(window);

export function installBridge() {
  window.fetch = async (input: RequestInfo | URL, init?: RequestInit): Promise<Response> => {
    const path = typeof input === 'string' ? input : input instanceof URL ? input.pathname : new URL(input.url).pathname;
    if (!paths.has(path)) return originalFetch(input, init);
    const method = (init?.method || 'GET').toUpperCase();
    let body: unknown;
    try { body = init?.body ? JSON.parse(String(init.body)) : undefined; }
    catch { return response({status:400,body:{error:'Nội dung không hợp lệ.'}}); }
    if (window.PORTAL_PREVIEW_DATA) {
      if (method !== 'GET') return response({status:403,body:{error:'Bản xem thử không lưu dữ liệu. Cài bộ mã trên Apps Script để sử dụng chức năng này.'}});
      return response({status:200,body:window.PORTAL_PREVIEW_DATA[path]});
    }
    if (!window.google?.script?.run) return response({status:503,body:{error:'Mở cổng bằng địa chỉ ứng dụng Apps Script (/exec), hoặc mở tệp XEM_THU.html để xem thử.'}});
    // A typed, unpredictable review code prevents blind clicks in an iframe from saving.
    // A DOM dialog works in nested Google Sites frames without native prompt/confirm.
    if (method === 'POST' && !(await reviewSave(body))) return response({status:409,body:{error:'Chưa xác nhận lưu. Nội dung nhập vẫn được giữ.'}});
    return new Promise<Response>(resolve => {
      window.google.script.run
        .withSuccessHandler((result: {status:number;body:unknown}) => resolve(response(result)))
        .withFailureHandler(() => resolve(response({status:503,body:{error:'Không kết nối được Apps Script. Kiểm tra tài khoản hoặc thử lại; nội dung nhập vẫn được giữ.'}})))
        .portalRequest({path,method,...(body === undefined ? {} : {body})});
    });
  };
}
function response(value:{status:number;body:unknown}) {
  return new Response(JSON.stringify(value.body),{status:value.status,headers:{'Content-Type':'application/json;charset=utf-8','Cache-Control':'no-store'}});
}

function reviewSave(body:any):Promise<boolean> {
  return new Promise(resolve => {
    const bytes = new Uint8Array(4); crypto.getRandomValues(bytes);
    const alphabet = '23456789ABCDEFGHJKLMNPQRSTUVWXYZ';
    const code = [...bytes].map(n => alphabet[n % alphabet.length]).join('');
    const host = document.querySelector<HTMLElement>('[data-slot="dialog-content"]');
    const oldOverflow = host?.style.overflow;
    if (host) {host.scrollTop=0;host.style.overflow='hidden';}
    const previousFocus = document.activeElement as HTMLElement | null;
    const backdrop = document.createElement('div'); backdrop.className = 'save-review' + (host ? ' in-form' : '');
    const box = document.createElement('section'); box.setAttribute('role','dialog'); box.setAttribute('aria-modal','true'); box.setAttribute('aria-labelledby','save-review-title');
    const title = document.createElement('h2'); title.id='save-review-title'; title.textContent='Xác nhận ghi dữ liệu';
    const text = document.createElement('p'); const record = body?.record;
    text.textContent = body?.action === 'listing'
      ? `${record?.kind === 'demand' ? 'Nhu cầu' : 'Năng lực'}: ${record?.org} · ${record?.profession} · ${record?.level} · ${record?.period} · ${record?.quantity} người.`
      : `Đề xuất đáp ứng ${record?.quantity} người. Phương án: ${record?.plan || ''}`;
    const label = document.createElement('label'); label.htmlFor='review-code'; label.textContent=`Nhập mã ${code} để xác nhận lưu vào bảng dữ liệu của cổng.`;
    const input = document.createElement('input'); input.id='review-code'; input.autocomplete='off'; input.maxLength=4; input.setAttribute('aria-label','Mã xác nhận ghi dữ liệu');
    const actions = document.createElement('div'); const cancel = document.createElement('button'); cancel.type='button'; cancel.textContent='Quay lại';
    const save = document.createElement('button'); save.type='button'; save.textContent='Xác nhận lưu'; save.disabled=true;
    const finish = (value:boolean) => {backdrop.remove();if(host)host.style.overflow=oldOverflow||'';document.removeEventListener('keydown',keyHandler,true);previousFocus?.focus();resolve(value);};
    const keyHandler=(event:KeyboardEvent)=>{if(event.key==='Escape'){event.preventDefault();event.stopImmediatePropagation();finish(false);}if(event.key==='Tab'){const items=[input,cancel,...(!save.disabled?[save]:[])];const first=items[0],last=items[items.length-1];if(event.shiftKey&&document.activeElement===first){event.preventDefault();last.focus();}else if(!event.shiftKey&&document.activeElement===last){event.preventDefault();first.focus();}}};
    input.addEventListener('input',()=>{save.disabled=input.value.trim().toUpperCase()!==code;});
    cancel.addEventListener('click',()=>finish(false));save.addEventListener('click',()=>{if(input.value.trim().toUpperCase()===code)finish(true);});
    document.addEventListener('keydown',keyHandler,true);
    actions.append(cancel,save);box.append(title,text,label,input,actions);backdrop.append(box);(host||document.body).append(backdrop);input.focus();
  });
}
