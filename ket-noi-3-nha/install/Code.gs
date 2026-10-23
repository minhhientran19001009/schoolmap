/** Cổng kết nối 3 nhà – Google Apps Script + Google Sheets.
 * Chạy setupPortal một lần trong trình biên tập trước khi triển khai.
 * Mặc định chỉ chủ sở hữu; không tự chia sẻ bảng tính hay công khai ứng dụng.
 */
var PORTAL_TABLES_ = {
  listings: 'NhuCau_NangLuc', proposals: 'DeXuat',
  employers: 'DoanhNghiep', schools: 'CoSoGDNN', metadata: 'Nguon'
};

function setupPortal() {
  var active = activeEmail_();
  var effective = String(Session.getEffectiveUser().getEmail() || '').toLowerCase();
  if (!active || active !== effective) throw new Error('Chạy setupPortal bằng tài khoản chủ sở hữu trong trình biên tập Apps Script.');
  var lock = LockService.getScriptLock();
  lock.waitLock(30000);
  try {
    var properties = PropertiesService.getScriptProperties();
    var owner = properties.getProperty('PORTAL_OWNER_EMAIL');
    if (owner && owner !== active) throw new Error('Dự án đã được cấu hình cho một chủ sở hữu khác.');
    var id = properties.getProperty('PORTAL_SPREADSHEET_ID');
    var book = id ? SpreadsheetApp.openById(id) : SpreadsheetApp.create('KẾT NỐI CUNG CẦU – Cổng 3 nhà');
    // Store the ID before initialization: a repeated run repairs incomplete setup.
    properties.setProperties({PORTAL_OWNER_EMAIL: active, PORTAL_SPREADSHEET_ID: book.getId()});
    ensureTable_(book, PORTAL_TABLES_.listings, PortalRules.SEED.listings);
    ensureTable_(book, PORTAL_TABLES_.proposals, PortalRules.SEED.proposals);
    ensureTable_(book, PORTAL_TABLES_.employers, PORTAL_DATA_.employers);
    ensureTable_(book, PORTAL_TABLES_.schools, PORTAL_DATA_.schools);
    ensureTable_(book, PORTAL_TABLES_.metadata, [
      {id: 'training', value: PORTAL_DATA_.source},
      {id: 'employers', value: PORTAL_DATA_.reviewedAt},
      {id: 'export', value: PORTAL_DATA_.exportInfo}
    ]);
    SpreadsheetApp.flush();
    var url = book.getUrl();
    console.log('Bảng dữ liệu riêng tư: ' + url);
    return {spreadsheetUrl: url, owner: active};
  } finally { lock.releaseLock(); }
}

function doGet() {
  try {
    assertOwner_();
    return HtmlService.createHtmlOutputFromFile('Index')
      .setTitle('KẾT NỐI CUNG CẦU | Cổng 3 nhà Ninh Bình')
      .addMetaTag('viewport', 'width=device-width, initial-scale=1')
      .setXFrameOptionsMode(HtmlService.XFrameOptionsMode.ALLOWALL);
    return HtmlService.createHtmlOutput('<!doctype html><html lang="vi"><body style="font-family:Arial;padding:32px;line-height:1.6"><h2>Cổng kết nối 3 nhà</h2><p>' + escapeHtml_(error.message) + '</p><p>Chạy setupPortal trong trình biên tập Apps Script nếu chưa cấu hình.</p></body></html>');
  }
}

/** Only public data RPC. Every call checks the signed-in identity server-side. */
function portalRequest(request) {
  try {
    assertOwner_();
    if (!request || typeof request !== 'object' || Array.isArray(request)) return reply_(400, {error: 'Nội dung không hợp lệ.'});
    var size;
    try { size = JSON.stringify(request).length; } catch (error) { return reply_(400, {error: 'Nội dung không hợp lệ.'}); }
    if (size > 24000) return reply_(413, {error: 'Nội dung quá dài.'});
    var method = String(request.method || 'GET').toUpperCase();
    var path = request.path;
    if (method === 'GET') {
      var book = openBook_();
      if (path === '/api/portal') return reply_(200, loadPortal_(book));
      if (path === '/api/employers') return reply_(200, {employers: readTable_(book, PORTAL_TABLES_.employers), reviewedAt: metadata_(book, 'employers')});
      if (path === '/api/training-schools') return reply_(200, {schools: readTable_(book, PORTAL_TABLES_.schools), source: metadata_(book, 'training')});
      return reply_(404, {error: 'Không có chức năng này.'});
    }
    if (method !== 'POST' || path !== '/api/portal') return reply_(405, {error: 'Thao tác không hợp lệ.'});
    return saveRecord_(request.body);
  } catch (error) {
    if (error.portalForbidden) return reply_(403, {error: error.message});
    console.error('Portal error: ' + String(error.message || error));
    return reply_(503, {error: 'Chưa truy cập được bảng dữ liệu. Kiểm tra cấu hình hoặc thử lại; nội dung nhập vẫn được giữ.'});
  }
}

function saveRecord_(body) {
  if (!body || typeof body !== 'object' || Array.isArray(body)) return reply_(400, {error: 'Nội dung không hợp lệ.'});
  var schema = body.action === 'listing' ? PortalRules.listingSchema : body.action === 'proposal' ? PortalRules.proposalSchema : null;
  if (!schema) return reply_(400, {error: 'Thao tác không hợp lệ.'});
  var parsed = schema.safeParse(body.record);
  if (!parsed.success) return reply_(400, {error: body.action === 'listing' ? 'Kiểm tra thông tin bắt buộc, số lượng và danh sách kỹ năng.' : 'Chọn nhu cầu, năng lực, số lượng và phương án ít nhất 10 ký tự.'});
  // All writers use the same lock. Re-read remaining quantities inside the lock.
  var lock = LockService.getScriptLock();
  if (!lock.tryLock(30000)) return reply_(503, {error: 'Có người đang lưu dữ liệu. Vui lòng thử lại.'});
  try {
    assertOwner_();
    var book = openBook_();
    var record = parsed.data;
    if (body.action === 'proposal') {
      var data = loadPortal_(book);
      var demand = data.listings.find(function (r) { return r.id === record.demandId; });
      var capacity = data.listings.find(function (r) { return r.id === record.capacityId; });
      if (!demand || !capacity || !PortalRules.matches(demand, capacity) ||
          record.quantity > PortalRules.remaining(demand, data.proposals) ||
          record.quantity > PortalRules.remaining(capacity, data.proposals)) {
        return reply_(409, {error: 'Không thể đề xuất: nghề, trình độ, kỳ chưa khớp hoặc số lượng còn lại đã thay đổi. Hãy tải lại dữ liệu.'});
      }
    }
    record = Object.assign({}, record, {id: Utilities.getUuid(), sample: 0, createdAt: new Date().toISOString()});
    appendRecord_(book, body.action === 'listing' ? PORTAL_TABLES_.listings : PORTAL_TABLES_.proposals, record);
    SpreadsheetApp.flush();
    return reply_(201, {id: record.id});
  } finally { lock.releaseLock(); }
}

function activeEmail_() { return String(Session.getActiveUser().getEmail() || '').trim().toLowerCase(); }
function assertOwner_() {
  var id = PropertiesService.getScriptProperties().getProperty('PORTAL_SPREADSHEET_ID');
  if (!id) {
    var error = new Error('Cổng chưa được cấu hình. Chạy setupPortal trong trình biên tập Apps Script.');
    error.portalForbidden = true;
    throw error;
  }
}
function openBook_() {
  var id = PropertiesService.getScriptProperties().getProperty('PORTAL_SPREADSHEET_ID');
  if (!id) throw new Error('Chưa chạy setupPortal.');
  return SpreadsheetApp.openById(id);
}
function ensureTable_(book, name, initial) {
  var sheet = book.getSheetByName(name) || book.insertSheet(name);
  if (sheet.getLastRow() === 0) {
    var rows = [['id', 'record_json']].concat(initial.map(function (r) { return [r.id, JSON.stringify(r)]; }));
    sheet.getRange(1, 1, rows.length, 2).setNumberFormat('@').setValues(rows);
    sheet.setFrozenRows(1);
    sheet.setColumnWidth(1, 210);
    sheet.setColumnWidth(2, 700);
  }
}
function readTable_(book, name) {
  var sheet = book.getSheetByName(name);
  if (!sheet) throw new Error('Thiếu trang dữ liệu: ' + name);
  var count = sheet.getLastRow();
  if (count < 2) return [];
  return sheet.getRange(2, 2, count - 1, 1).getValues().map(function (row) {
    if (typeof row[0] !== 'string' || !row[0]) throw new Error('Dòng dữ liệu hỏng trong ' + name);
    return JSON.parse(row[0]);
  });
}
function appendRecord_(book, name, record) {
  var sheet = book.getSheetByName(name);
  if (!sheet) throw new Error('Thiếu trang dữ liệu: ' + name);
  // Text cells and JSON prevent spreadsheet formula evaluation from user text.
  sheet.getRange(sheet.getLastRow() + 1, 1, 1, 2).setNumberFormat('@').setValues([[record.id, JSON.stringify(record)]]);
}
function metadata_(book, id) {
  var item = readTable_(book, PORTAL_TABLES_.metadata).find(function (r) { return r.id === id; });
  if (!item) throw new Error('Thiếu thông tin nguồn.');
  return item.value;
}
function loadPortal_(book) { return {listings: readTable_(book, PORTAL_TABLES_.listings), proposals: readTable_(book, PORTAL_TABLES_.proposals)}; }
function reply_(status, body) { return {status: status, body: body}; }
function escapeHtml_(value) { return String(value).replace(/[&<>"']/g, function (s) { return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[s]; }); }
