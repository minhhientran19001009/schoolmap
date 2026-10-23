import initialJobs from '../data/jobPostings.json'

// Comprehensive catalog of enterprises across Ninh Bình & vicinity
export const ENTERPRISE_INDUSTRIES = [
  { id: 'all', name: 'Tất cả lĩnh vực', icon: 'fa-solid fa-layer-group' },
  { id: 'dien_tu', name: 'Điện tử, viễn thông, máy tính', icon: 'fa-solid fa-microchip' },
  { id: 'o_to_co_khi', name: 'Ô tô, xe máy & Cơ khí', icon: 'fa-solid fa-wrench' },
  { id: 'da_giay', name: 'Da giày', icon: 'fa-solid fa-shoe-prints' },
  { id: 'det_may', name: 'Dệt may, thời trang', icon: 'fa-solid fa-shirt' },
  { id: 'linh_kien', name: 'Linh kiện & Công nghiệp hỗ trợ', icon: 'fa-solid fa-gears' },
  { id: 'du_lich_dich_vu', name: 'Du lịch, khách sạn & Dịch vụ', icon: 'fa-solid fa-hotel' },
  { id: 'nong_nghiep_tp', name: 'Nông nghiệp & Chế biến thực phẩm', icon: 'fa-solid fa-seedling' },
  { id: 'cntt_phan_mem', name: 'Công nghệ thông tin & Phần mềm', icon: 'fa-solid fa-laptop-code' },
  { id: 'xay_dung', name: 'Xây dựng & Kiến trúc', icon: 'fa-solid fa-trowel-bricks' },
  { id: 'duoc_hoa_chat', name: 'Dược phẩm, hóa chất & Năng lượng', icon: 'fa-solid fa-flask' }
]

export const ENTERPRISE_REGIONS = [
  { id: 'all', name: 'Tất cả khu vực / KCN' },
  { id: 'kcn_gian_khau', name: 'KCN Gián Khẩu (Gia Viễn)' },
  { id: 'kcn_phuc_son', name: 'KCN Phúc Sơn (TP. Ninh Bình)' },
  { id: 'kcn_tam_diep', name: 'KCN Tam Điệp (TP. Tam Điệp)' },
  { id: 'kcn_dong_van', name: 'KCN Đồng Văn / Duy Hà' },
  { id: 'kcn_bao_minh', name: 'KCN Bảo Minh' },
  { id: 'kcn_my_thuan', name: 'KCN Mỹ Thuận' },
  { id: 'tp_ninh_binh', name: 'TP. Ninh Bình' },
  { id: 'tp_tam_diep', name: 'TP. Tam Điệp' }
]

export const ENTERPRISE_SCALES = [
  { id: 'all', label: 'Tất cả quy mô lao động' },
  { id: 'above_5000', label: 'Trên 5.000 lao động' },
  { id: '1000_5000', label: '1.000 - 5.000 lao động' },
  { id: '500_1000', label: '500 - 1.000 lao động' },
  { id: 'under_500', label: 'Dưới 500 lao động' }
]

// Base dataset combining FDI large-scale employers and local recruiting companies
const BASE_ENTERPRISES = [
  {
    id: 50,
    name: 'Công ty Cổ phần sản xuất ô tô Hyundai Thành Công Việt Nam',
    short_name: 'Hyundai Thành Công',
    logo: 'HTCV',
    color: 'bg-blue-700',
    industry: 'o_to_co_khi',
    industry_name: 'Ô tô, xe máy & Cơ khí',
    region: 'kcn_gian_khau',
    region_name: 'KCN Gián Khẩu',
    employee_count: 1422,
    address: 'KCN Gián Khẩu, Tỉnh Ninh Bình',
    website: 'https://hyundai-thanhcong.vn',
    tax_code: '2700288888',
    phone: '0229 386 8686',
    email: 'tuyendung@hyundai-thanhcong.vn',
    is_verified: true,
    is_fdi: false,
    partner_schools: ['Trường Cao đẳng Cơ giới Ninh Bình', 'Trường Cao đẳng Cơ điện Xây dựng Việt Xô'],
    description: 'Tập đoàn sản xuất, lắp ráp ô tô hàng đầu Việt Nam với nhà máy hiện đại quy mô lớn đặt tại KCN Gián Khẩu, tỉnh Ninh Bình.'
  },
  {
    id: 27,
    name: 'Công ty TNHH Khoa học kỹ thuật AVC (Việt Nam)',
    short_name: 'AVC Technology',
    logo: 'AVC',
    color: 'bg-indigo-700',
    industry: 'dien_tu',
    industry_name: 'Điện tử, viễn thông, máy tính',
    region: 'kcn_dong_van',
    region_name: 'KCN Đồng Văn III',
    employee_count: 10612,
    address: 'Lô CN05, KCN hỗ trợ Đồng Văn III, phường Duy Hà, Ninh Bình',
    website: 'https://avc.com.tw',
    tax_code: '0700812345',
    phone: '0229 398 8888',
    email: 'hr-vn@avc.com',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ điện và Thủy lợi', 'Trường Cao đẳng Nghề Hà Nam'],
    description: 'Doanh nghiệp FDI công nghệ cao chuyên sản xuất linh kiện tản nhiệt và thiết bị máy tính quy mô trên 10.000 lao động.'
  },
  {
    id: 49,
    name: 'Công ty Honda Việt Nam - Chi nhánh Hà Nam',
    short_name: 'Honda Việt Nam',
    logo: 'HONDA',
    color: 'bg-red-700',
    industry: 'o_to_co_khi',
    industry_name: 'Ô tô, xe máy & Cơ khí',
    region: 'kcn_dong_van',
    region_name: 'KCN Đồng Văn II',
    employee_count: 902,
    address: 'KCN Đồng Văn II, phường Duy Hà, Ninh Bình',
    website: 'https://honda.com.vn',
    tax_code: '2500150335-002',
    phone: '0229 385 6666',
    email: 'recruitment@honda.com.vn',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ điện Xây dựng Việt Xô', 'Trường Cao đẳng Cơ giới Ninh Bình'],
    description: 'Nhà máy sản xuất bánh răng, phụ tùng động cơ xe máy và ô tô của thương hiệu Honda Nhật Bản.'
  },
  {
    id: 28,
    name: 'Công ty TNHH Golden Victory Việt Nam',
    short_name: 'Golden Victory',
    logo: 'GV',
    color: 'bg-amber-600',
    industry: 'da_giay',
    industry_name: 'Da giày',
    region: 'all',
    region_name: 'Đồng Thịnh, Ninh Bình',
    employee_count: 9206,
    address: 'Xóm Đông Bình, xã Đồng Thịnh, Ninh Bình',
    website: null,
    tax_code: '0601018899',
    phone: '0229 389 1234',
    email: 'tuyendung@goldenvictory.com',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Trung cấp Nghề Kỹ thuật & May mặc'],
    description: 'Doanh nghiệp FDI sản xuất giày thể thao xuất khẩu hàng đầu khu vực với hơn 9.200 cán bộ công nhân viên.'
  },
  {
    id: 29,
    name: 'Công ty TNHH Giày Amara Việt Nam',
    short_name: 'Amara Shoes',
    logo: 'AMARA',
    color: 'bg-emerald-700',
    industry: 'da_giay',
    industry_name: 'Da giày',
    region: 'all',
    region_name: 'Cổ Lễ, Ninh Bình',
    employee_count: 9202,
    address: 'Song Khê, xã Cổ Lễ, Ninh Bình',
    website: null,
    tax_code: '0601056789',
    phone: '0229 378 9999',
    email: 'hr@amarashoes.com.vn',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Trung cấp Nghề Ninh Bình'],
    description: 'Tập đoàn gia công da giày thời trang xuất khẩu chất lượng cao sang thị trường EU và Bắc Mỹ.'
  },
  {
    id: 30,
    name: 'Công ty TNHH Youngone Nam Định',
    short_name: 'Youngone Group',
    logo: 'YOUNG',
    color: 'bg-sky-700',
    industry: 'det_may',
    industry_name: 'Dệt may, thời trang',
    region: 'all',
    region_name: 'KCN Hòa Xá',
    employee_count: 8669,
    address: 'Lô O, P, Q, R, đường N6, KCN Hòa Xá, Ninh Bình',
    website: 'https://youngone.com',
    tax_code: '0600345678',
    phone: '0229 384 5555',
    email: 'careers@youngone.com.vn',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Kinh tế - Kỹ thuật'],
    description: 'Doanh nghiệp FDI may trang phục thể thao dã ngoại cao cấp (The North Face, Nike, Patagonia).'
  },
  {
    id: 31,
    name: 'Công ty TNHH QMH Computer',
    short_name: 'QMH Computer',
    logo: 'QMH',
    color: 'bg-blue-600',
    industry: 'dien_tu',
    industry_name: 'Điện tử, viễn thông, máy tính',
    region: 'kcn_my_thuan',
    region_name: 'KCN Mỹ Thuận',
    employee_count: 7682,
    address: 'Lô CN14, KCN Mỹ Thuận, Ninh Bình',
    website: null,
    tax_code: '0601178901',
    phone: '0229 392 3333',
    email: 'hr@qmh-computer.com',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ giới Ninh Bình'],
    description: 'Tổ hợp sản xuất linh kiện bo mạch chủ và lắp ráp máy tính công nghệ cao thuộc chuỗi cung ứng toàn cầu.'
  },
  {
    id: 32,
    name: 'Công ty TNHH Neweb Việt Nam',
    short_name: 'Neweb Corp',
    logo: 'NEWEB',
    color: 'bg-teal-700',
    industry: 'dien_tu',
    industry_name: 'Điện tử, viễn thông, máy tính',
    region: 'kcn_dong_van',
    region_name: 'KCN Đồng Văn III',
    employee_count: 7080,
    address: 'KCN Đồng Văn III, phường Đồng Văn, Ninh Bình',
    website: 'https://wnc.com.tw',
    tax_code: '0700823456',
    phone: '0229 396 7777',
    email: 'recruit.vn@wnc.com.tw',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ điện và Thủy lợi'],
    description: 'Chuyên sản xuất thiết bị mạng viễn thông không dây (Wi-Fi router, 5G module, ăng-ten thông minh).'
  },
  {
    id: 33,
    name: 'Công ty TNHH Wistron Infocomm (Việt Nam)',
    short_name: 'Wistron Infocomm',
    logo: 'WISTRON',
    color: 'bg-cyan-800',
    industry: 'dien_tu',
    industry_name: 'Điện tử, viễn thông, máy tính',
    region: 'kcn_dong_van',
    region_name: 'KCN Đồng Văn III',
    employee_count: 6529,
    address: 'Lô CN09, CN10, KCN Đồng Văn III, Ninh Bình',
    website: 'https://wistron.com',
    tax_code: '0700834567',
    phone: '0229 397 1234',
    email: 'vn_recruitment@wistron.com',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ điện Xây dựng Việt Xô'],
    description: 'Tập đoàn ODM hàng đầu thế giới sản xuất máy tính xách tay và thiết bị điện tử công nghệ cao.'
  },
  {
    id: 34,
    name: 'Công ty TNHH Giày Athena Việt Nam',
    short_name: 'Giày Athena',
    logo: 'ATHENA',
    color: 'bg-purple-700',
    industry: 'da_giay',
    industry_name: 'Da giày',
    region: 'all',
    region_name: 'Yên Mạc, Ninh Bình',
    employee_count: 6270,
    address: 'Đông Đoài, xã Yên Mạc, Ninh Bình',
    website: null,
    tax_code: '2700345678',
    phone: '0229 383 6789',
    email: 'tuyendung@athenashoes.vn',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Trung cấp Nghề Kim Sơn'],
    description: 'Nhà máy sản xuất giày dép quy mô lớn tạo việc làm cho hàng nghìn lao động huyện Yên Mô và lân cận.'
  },
  {
    id: 35,
    name: 'Công ty TNHH Giày ADORA Việt Nam',
    short_name: 'Giày ADORA',
    logo: 'ADORA',
    color: 'bg-rose-700',
    industry: 'da_giay',
    industry_name: 'Da giày',
    region: 'kcn_tam_diep',
    region_name: 'KCN Tam Điệp',
    employee_count: 6131,
    address: 'KCN Tam Điệp, phường Tam Điệp, Ninh Bình',
    website: null,
    tax_code: '2700456789',
    phone: '0229 382 1122',
    email: 'tuyendung@adorashoes.vn',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ giới Ninh Bình'],
    description: 'Doanh nghiệp FDI da giày lớn tại TP. Tam Điệp với chế độ đãi ngộ tốt và môi trường làm việc ổn định.'
  },
  {
    id: 37,
    name: 'Công ty TNHH Vienergy',
    short_name: 'Vienergy Shoes',
    logo: 'VIENERGY',
    color: 'bg-emerald-800',
    industry: 'da_giay',
    industry_name: 'Da giày',
    region: 'kcn_phuc_son',
    region_name: 'KCN Phúc Sơn',
    employee_count: 5201,
    address: 'KCN Phúc Sơn, phường Nam Hoa Lư, Ninh Bình',
    website: null,
    tax_code: '2700567890',
    phone: '0229 389 4567',
    email: 'hr@vienergy.com.vn',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ giới Ninh Bình'],
    description: 'Doanh nghiệp FDI da giày thể thao hiện đại tọa lạc tại KCN Phúc Sơn, TP. Ninh Bình.'
  },
  {
    id: 39,
    name: 'Công ty TNHH Hệ thống dây dẫn Sumi Việt Nam',
    short_name: 'Sumi Wiring Systems',
    logo: 'SUMI',
    color: 'bg-blue-800',
    industry: 'linh_kien',
    industry_name: 'Linh kiện & Công nghiệp hỗ trợ',
    region: 'kcn_dong_van',
    region_name: 'KCN Đồng Văn II',
    employee_count: 4218,
    address: 'Lô B-1, KCN Đồng Văn II, phường Duy Hà, Ninh Bình',
    website: 'https://sws.co.jp',
    tax_code: '0700412345',
    phone: '0229 398 5555',
    email: 'recruitment@sumi.com.vn',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ điện Xây dựng Việt Xô', 'Trường Cao đẳng Cơ giới Ninh Bình'],
    description: 'Thành viên Tập đoàn Sumitomo Nhật Bản chuyên sản xuất bộ dây dẫn điện (wire harness) cho xe ô tô.'
  },
  {
    id: 40,
    name: 'Công ty TNHH Yokowo Việt Nam',
    short_name: 'Yokowo Việt Nam',
    logo: 'YOKOWO',
    color: 'bg-indigo-800',
    industry: 'linh_kien',
    industry_name: 'Linh kiện & Công nghiệp hỗ trợ',
    region: 'kcn_dong_van',
    region_name: 'KCN Đồng Văn II',
    employee_count: 3528,
    address: 'KCN Đồng Văn II, phường Duy Hà, Ninh Bình',
    website: 'https://yokowo.com.vn',
    tax_code: '0700523456',
    phone: '0229 398 3333',
    email: 'hr-yokowo@yokowo.com.vn',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ điện và Thủy lợi'],
    description: 'Doanh nghiệp Nhật Bản sản xuất ăng-ten ô tô, thiết bị kết nối tinh xảo và linh kiện ô tô hiện đại.'
  },
  {
    id: 43,
    name: 'Công ty TNHH MCNEX Vina',
    short_name: 'MCNEX Vina',
    logo: 'MCNEX',
    color: 'bg-blue-900',
    industry: 'dien_tu',
    industry_name: 'Điện tử, viễn thông, máy tính',
    region: 'kcn_phuc_son',
    region_name: 'KCN Phúc Sơn',
    employee_count: 2968,
    address: 'Lô CN1, KCN Phúc Sơn, phường Nam Hoa Lư, Ninh Bình',
    website: 'https://mcnex.com',
    tax_code: '2700678901',
    phone: '0229 387 6789',
    email: 'mcnexvina@mcnex.com',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ giới Ninh Bình'],
    description: 'Doanh nghiệp Hàn Quốc công nghệ cao sản xuất camera module cho điện thoại thông minh và ô tô tự hành.'
  },
  {
    id: 45,
    name: 'Công ty Cổ phần May Sông Hồng',
    short_name: 'May Sông Hồng',
    logo: 'MSH',
    color: 'bg-rose-800',
    industry: 'det_may',
    industry_name: 'Dệt may, thời trang',
    region: 'all',
    region_name: 'TP. Nam Định',
    employee_count: 2542,
    address: '105 Nguyễn Đức Thuận, TP. Nam Định, Ninh Bình',
    website: 'https://songhong.vn',
    tax_code: '0600123456',
    phone: '0228 364 9999',
    email: 'contact@songhong.vn',
    is_verified: true,
    is_fdi: false,
    partner_schools: ['Trường Cao đẳng Kinh tế - Kỹ thuật Ninh Bình'],
    description: 'Thương hiệu chăn ga gối đệm và may mặc xuất khẩu uy tín hàng đầu Việt Nam.'
  },
  {
    id: 47,
    name: 'Công ty TNHH Seoul Semiconductor Vina',
    short_name: 'Seoul Semi',
    logo: 'SEOUL',
    color: 'bg-cyan-700',
    industry: 'dien_tu',
    industry_name: 'Điện tử, viễn thông, máy tính',
    region: 'kcn_dong_van',
    region_name: 'KCN Đồng Văn I',
    employee_count: 2143,
    address: 'KCN Đồng Văn I, phường Đồng Văn, Ninh Bình',
    website: 'https://seoulsemicon.com',
    tax_code: '0700634567',
    phone: '0229 396 8888',
    email: 'seoulvina_recruit@seoulsemicon.com',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ điện và Thủy lợi'],
    description: 'Tập đoàn bán dẫn Hàn Quốc phát triển công nghệ đèn LED đóng gói tiên tiến cho ngành chiếu sáng và ô tô.'
  },
  {
    id: 101,
    name: 'Công ty Cổ phần Cơ điện Lilama Ninh Bình',
    short_name: 'Lilama Ninh Bình',
    logo: 'LILAMA',
    color: 'bg-amber-600',
    industry: 'o_to_co_khi',
    industry_name: 'Ô tô, xe máy & Cơ khí',
    region: 'all',
    region_name: 'Châu Sơn',
    employee_count: 650,
    address: 'KCN Châu Sơn, Tỉnh Ninh Bình',
    website: 'http://lilamaninhbinh.com.vn',
    tax_code: '2700112233',
    phone: '0229 388 1234',
    email: 'tuyendung@lilamanb.vn',
    is_verified: true,
    is_fdi: false,
    partner_schools: ['Trường Cao đẳng Cơ giới Ninh Bình', 'Trường CĐ nghề số 20/BQP'],
    description: 'Doanh nghiệp cơ khí chế tạo, lắp ráp thiết bị công nghiệp nặng và đường ống áp lực chuyên nghiệp.'
  },
  {
    id: 102,
    name: 'Công ty TNHH Phụ tùng Ô tô Sejin F&D Ninh Bình',
    short_name: 'Sejin F&D',
    logo: 'SEJIN',
    color: 'bg-blue-600',
    industry: 'o_to_co_khi',
    industry_name: 'Ô tô, xe máy & Cơ khí',
    region: 'kcn_dong_van',
    region_name: 'KCN Duy Hà',
    employee_count: 520,
    address: 'KCN Duy Hà, Tỉnh Ninh Bình',
    website: null,
    tax_code: '2700889900',
    phone: '0229 388 1234',
    email: 'hr@sejinfd.com',
    is_verified: true,
    is_fdi: true,
    partner_schools: ['Trường Cao đẳng Cơ điện Xây dựng Việt Xô'],
    description: 'Gia công cơ khí chính xác CNC linh kiện gầm máy và cụm phụ tùng ô tô chất lượng cao theo tiêu chuẩn Hàn Quốc.'
  },
  {
    id: 103,
    name: 'Công ty CP Thực phẩm Xuất khẩu Đồng Giao (DOVECO)',
    short_name: 'DOVECO',
    logo: 'DOVECO',
    color: 'bg-emerald-700',
    industry: 'nong_nghiep_tp',
    industry_name: 'Nông nghiệp & Chế biến thực phẩm',
    region: 'tp_tam_diep',
    region_name: 'TP. Tam Điệp',
    employee_count: 1850,
    address: 'Trung Sơn, TP. Tam Điệp, Tỉnh Ninh Bình',
    website: 'https://doveco.com.vn',
    tax_code: '2700147852',
    phone: '0229 386 4225',
    email: 'contact@doveco.com.vn',
    is_verified: true,
    is_fdi: false,
    partner_schools: ['Trường Cao đẳng Nông nghiệp & Phát triển Nông thôn Bắc Bộ', 'Trường Cao đẳng Cơ giới Ninh Bình'],
    description: 'Doanh nghiệp nông sản thực phẩm hàng đầu cả nước với các sản phẩm dứa, ngô ngọt, vải thiều xuất khẩu sang 50+ quốc gia.'
  },
  {
    id: 104,
    name: 'Khách sạn The Reed Ninh Bình',
    short_name: 'The Reed Hotel',
    logo: 'REED',
    color: 'bg-indigo-900',
    industry: 'du_lich_dich_vu',
    industry_name: 'Du lịch, khách sạn & Dịch vụ',
    region: 'tp_ninh_binh',
    region_name: 'TP. Ninh Bình',
    employee_count: 320,
    address: 'Đinh Điền, Phường Đông Thành, TP. Ninh Bình',
    website: 'https://thereedhotel.com',
    tax_code: '2700778899',
    phone: '0229 388 9988',
    email: 'hr@thereedhotel.com',
    is_verified: true,
    is_fdi: false,
    partner_schools: ['Trường Cao đẳng Du lịch & Dịch vụ'],
    description: 'Khách sạn trung tâm hội nghị 4 sao quốc tế tiêu chuẩn hàng đầu phục vụ du lịch sinh thái và công vụ tỉnh Ninh Bình.'
  },
  {
    id: 105,
    name: 'Công ty CP Dược phẩm Ninh Bình (NIPHAPHARM)',
    short_name: 'NIPHAPHARM',
    logo: 'NIPHA',
    color: 'bg-teal-600',
    industry: 'duoc_hoa_chat',
    industry_name: 'Dược phẩm, hóa chất & Năng lượng',
    region: 'tp_ninh_binh',
    region_name: 'TP. Ninh Bình',
    employee_count: 450,
    address: 'Số 12 Đường Tràng An, Phường Nam Thành, TP. Ninh Bình',
    website: 'https://niphapharm.com.vn',
    tax_code: '2700223344',
    phone: '0229 387 1122',
    email: 'info@niphapharm.com.vn',
    is_verified: true,
    is_fdi: false,
    partner_schools: ['Trường Cao đẳng Y Dược Ninh Bình'],
    description: 'Đơn vị sản xuất dược phẩm đạt chuẩn GMP-WHO với các dòng sản phẩm thuốc đông dược và tân dược uy tín.'
  },
  {
    id: 106,
    name: 'Công ty Cổ phần Nhiệt điện Ninh Bình',
    short_name: 'Nhiệt điện Ninh Bình',
    logo: 'NBP',
    color: 'bg-orange-700',
    industry: 'duoc_hoa_chat',
    industry_name: 'Dược phẩm, hóa chất & Năng lượng',
    region: 'tp_ninh_binh',
    region_name: 'TP. Ninh Bình',
    employee_count: 850,
    address: 'Phường Thanh Bình, TP. Ninh Bình',
    website: 'https://nbi.evn.vn',
    tax_code: '2700282414',
    phone: '0229 387 1010',
    email: 'nhietdiennb@evn.vn',
    is_verified: true,
    is_fdi: false,
    partner_schools: ['Trường Cao đẳng Cơ điện Xây dựng Việt Xô'],
    description: 'Nhà máy phát điện truyền thống đóng góp nguồn năng lượng ổn định cho lưới điện quốc gia và công nghiệp tỉnh.'
  }
]

export const enterpriseService = {
  // Get all enterprises, merging with live active jobs from jobPostings.json
  getAll() {
    return BASE_ENTERPRISES.map(ent => {
      // Find open jobs matching this enterprise name or alias
      const matchingJobs = initialJobs.filter(job => {
        if (!job.company) return false
        const comp = job.company.toLowerCase()
        const entName = ent.name.toLowerCase()
        const shortName = (ent.short_name || '').toLowerCase()
        return comp.includes(shortName) || entName.includes(comp) || comp.includes(entName)
      })

      return {
        ...ent,
        jobs_count: matchingJobs.length,
        jobs: matchingJobs
      }
    })
  },

  getById(id) {
    const list = this.getAll()
    return list.find(e => String(e.id) === String(id)) || null
  },

  getIndustries() {
    return ENTERPRISE_INDUSTRIES
  },

  getRegions() {
    return ENTERPRISE_REGIONS
  },

  getScales() {
    return ENTERPRISE_SCALES
  },

  // Filter enterprises by multi-criteria
  filterEnterprises({ search = '', industry = 'all', region = 'all', scale = 'all', hasJobsOnly = false }) {
    let result = this.getAll()

    // 1. Search keyword
    if (search && search.trim()) {
      const q = search.trim().toLowerCase()
      result = result.filter(e => 
        e.name.toLowerCase().includes(q) ||
        (e.short_name && e.short_name.toLowerCase().includes(q)) ||
        (e.address && e.address.toLowerCase().includes(q)) ||
        (e.tax_code && e.tax_code.includes(q)) ||
        (e.industry_name && e.industry_name.toLowerCase().includes(q)) ||
        (e.region_name && e.region_name.toLowerCase().includes(q))
      )
    }

    // 2. Filter by industry
    if (industry && industry !== 'all') {
      result = result.filter(e => e.industry === industry)
    }

    // 3. Filter by region
    if (region && region !== 'all') {
      result = result.filter(e => e.region === region)
    }

    // 4. Filter by workforce scale
    if (scale && scale !== 'all') {
      if (scale === 'above_5000') {
        result = result.filter(e => e.employee_count >= 5000)
      } else if (scale === '1000_5000') {
        result = result.filter(e => e.employee_count >= 1000 && e.employee_count < 5000)
      } else if (scale === '500_1000') {
        result = result.filter(e => e.employee_count >= 500 && e.employee_count < 1000)
      } else if (scale === 'under_500') {
        result = result.filter(e => e.employee_count < 500)
      }
    }

    // 5. Filter only hiring enterprises
    if (hasJobsOnly) {
      result = result.filter(e => e.jobs_count > 0)
    }

    return result
  }
}
