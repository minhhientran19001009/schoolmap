const fs = require('fs');
const path = require('path');

const filePath = path.join(__dirname, '..', 'src', 'data', 'jobPostings.json');
const jobs = JSON.parse(fs.readFileSync(filePath, 'utf8'));

const enrichedData = [
  {
    id: "job-1",
    profession: "Công nghệ ô tô",
    recruitment_count: 180,
    skills: ["Lắp ráp linh kiện", "Vận hành rô bốt", "Công nghệ ô tô", "An toàn 5S"],
    training_schools: [
      {
        name: "Trường Cao đẳng Cơ giới Ninh Bình",
        level: "Cao đẳng · Trung cấp",
        district: "TP. Tam Điệp",
        program: "Công nghệ ô tô",
        duration: "Khóa đào tạo 2 - 3 năm"
      },
      {
        name: "Trường CĐ nghề số 20/BQP",
        level: "Trung cấp",
        district: "Phủ Lý",
        program: "Công nghệ ô tô",
        duration: "Khóa đào tạo nghề 2 năm"
      }
    ],
    demands: [
      { company: "Công ty CP Sản xuất Ô tô Hyundai Thành Công Việt Nam", quantity: 180 }
    ]
  },
  {
    id: "job-2",
    profession: "Kỹ thuật hàn",
    recruitment_count: 120,
    skills: ["Hàn MIG/MAG", "Hàn TIG", "Đọc bản vẽ", "Đo kiểm siêu âm"],
    training_schools: [
      {
        name: "Trường Cao đẳng Cơ điện Xây dựng Việt Xô",
        level: "Cao đẳng · Trung cấp",
        district: "TP. Tam Điệp",
        program: "Kỹ thuật hàn",
        duration: "Khóa cao đẳng & chứng chỉ 3G/6G"
      },
      {
        name: "Trường Trung cấp Kinh tế - Kỹ thuật Đổi Mới",
        level: "Trung cấp",
        district: "Yên Khánh",
        program: "Hàn công nghiệp",
        duration: "Khóa trung cấp nghề"
      }
    ],
    demands: [
      { company: "Công ty Cổ phần Cơ điện Lilama Ninh Bình", quantity: 120 }
    ]
  },
  {
    id: "job-3",
    profession: "Cơ điện tử",
    recruitment_count: 180,
    skills: ["PLC", "Lắp ráp", "Tiếng Trung cơ bản"],
    training_schools: [
      {
        name: "Cơ sở GDNN A (mẫu)",
        level: "Cao đẳng",
        district: "Khu vực Hà Nam",
        program: "Cơ điện tử",
        duration: "Bổ sung kỹ năng: 8 tuần"
      },
      {
        name: "Trường CĐ nghề số 20/BQP",
        level: "Trung cấp",
        district: "Phủ Lý",
        program: "Cơ điện tử",
        duration: "Khóa đào tạo nghề"
      }
    ],
    demands: [
      { company: "Doanh nghiệp Điện tử A (mẫu)", quantity: 180 }
    ]
  },
  {
    id: "job-4",
    profession: "Điện công nghiệp",
    recruitment_count: 120,
    skills: ["An toàn điện", "Bảo trì", "Đọc bản vẽ"],
    training_schools: [
      {
        name: "Cơ sở GDNN B (mẫu)",
        level: "Trung cấp",
        district: "Khu vực Ninh Bình",
        program: "Điện công nghiệp",
        duration: "Bổ sung kỹ năng: 6 tuần"
      },
      {
        name: "Trường Cao đẳng Cơ giới Ninh Bình",
        level: "Cao đẳng",
        district: "TP. Tam Điệp",
        program: "Điện công nghiệp & Tự động hóa",
        duration: "Đào tạo 3 năm"
      }
    ],
    demands: [
      { company: "Doanh nghiệp Cơ khí B (mẫu)", quantity: 120 }
    ]
  },
  {
    id: "job-5",
    profession: "May công nghiệp",
    recruitment_count: 240,
    skills: ["May công nghiệp", "Kiểm tra chất lượng"],
    training_schools: [
      {
        name: "Cơ sở GDNN C (mẫu)",
        level: "Sơ cấp",
        district: "Khu vực Nam Định",
        program: "May công nghiệp",
        duration: "Khóa sơ cấp: 3 tháng"
      },
      {
        name: "Trung tâm GDNN - GDTX Kim Sơn",
        level: "Sơ cấp",
        district: "Kim Sơn",
        program: "May may thời trang",
        duration: "Khóa đào tạo nghề ngắn hạn"
      }
    ],
    demands: [
      { company: "Doanh nghiệp Dệt may C (mẫu)", quantity: 240 }
    ]
  },
  {
    id: "job-6",
    profession: "Nghiệp vụ nhà hàng",
    recruitment_count: 60,
    skills: ["Phục vụ bàn", "Tiếng Anh giao tiếp"],
    training_schools: [
      {
        name: "Cơ sở GDNN D (mẫu)",
        level: "Sơ cấp",
        district: "Khu vực Ninh Bình",
        program: "Nghiệp vụ nhà hàng",
        duration: "Khóa sơ cấp: 3 tháng"
      },
      {
        name: "Trường Cao đẳng Y tế & Du lịch Ninh Bình",
        level: "Trung cấp · Sơ cấp",
        district: "TP. Ninh Bình",
        program: "Quản trị lễ tân & Nhà hàng",
        duration: "Khóa trung cấp 2 năm"
      }
    ],
    demands: [
      { company: "Doanh nghiệp Du lịch D (mẫu)", quantity: 60 }
    ]
  },
  {
    id: "job-7",
    profession: "Kỹ thuật chế biến món ăn",
    recruitment_count: 45,
    skills: ["Bếp Á", "Bảo quản thực phẩm", "Vệ sinh an toàn HACCP", "Tốc độ ra món"],
    training_schools: [
      {
        name: "Trường Cao đẳng Du lịch & Thương mại Ninh Bình",
        level: "Trung cấp · Sơ cấp",
        district: "TP. Ninh Bình",
        program: "Kỹ thuật chế biến món ăn",
        duration: "Khóa nghề 1 - 2 năm"
      },
      {
        name: "Trung tâm GDNN - GDTX Hoa Lư",
        level: "Sơ cấp",
        district: "Hoa Lư",
        program: "Nấu ăn ẩm thực đặc sản Tràng An",
        duration: "Khóa sơ cấp 3 tháng"
      }
    ],
    demands: [
      { company: "Quần thể Du lịch Sinh thái Tràng An (Doanh nghiệp Xuân Trường)", quantity: 45 }
    ]
  },
  {
    id: "job-8",
    profession: "Công nghệ thực phẩm",
    recruitment_count: 90,
    skills: ["Vận hành chiết rót", "Kiểm soát thanh trùng", "Tiêu chuẩn ISO 22000", "An toàn máy"],
    training_schools: [
      {
        name: "Trường Cao đẳng Cơ giới Ninh Bình",
        level: "Cao đẳng · Trung cấp",
        district: "TP. Tam Điệp",
        program: "Vận hành máy chế biến nông sản",
        duration: "Khóa 2 năm"
      },
      {
        name: "Trung tâm GDNN - GDTX TP. Tam Điệp",
        level: "Sơ cấp",
        district: "TP. Tam Điệp",
        program: "Kỹ thuật đóng hộp nông sản",
        duration: "Khóa sơ cấp 3 tháng"
      }
    ],
    demands: [
      { company: "Công ty CP Thực phẩm Xuất khẩu Đồng Giao (DOVECO)", quantity: 90 }
    ]
  },
  {
    id: "job-9",
    profession: "Vận hành máy thi công nền",
    recruitment_count: 70,
    skills: ["Lái máy xúc", "Lái máy lu", "Bảo dưỡng máy công trình", "An toàn công trường"],
    training_schools: [
      {
        name: "Trường Cao đẳng Cơ giới Ninh Bình",
        level: "Trung cấp · Sơ cấp",
        district: "TP. Tam Điệp",
        program: "Vận hành máy thi công nền",
        duration: "Cấp chứng chỉ nghề & bằng lái"
      },
      {
        name: "Trường Cao đẳng Cơ điện Xây dựng Việt Xô",
        level: "Sơ cấp",
        district: "TP. Tam Điệp",
        program: "Vận hành máy xây dựng",
        duration: "Khóa 3 - 6 tháng"
      }
    ],
    demands: [
      { company: "Doanh nghiệp Xây dựng Xuân Trường", quantity: 70 }
    ]
  },
  {
    id: "job-10",
    profession: "Vận hành thiết bị silicat & kính",
    recruitment_count: 60,
    skills: ["Điều khiển lò nung", "Đo độ phẳng & ứng suất", "Kiểm tra KCS kính", "An toàn nhiệt độ cao"],
    training_schools: [
      {
        name: "Trường Cao đẳng Cơ điện Xây dựng Việt Xô",
        level: "Trung cấp",
        district: "TP. Tam Điệp",
        program: "Vận hành dây chuyền vật liệu xây dựng",
        duration: "Khóa trung cấp 2 năm"
      },
      {
        name: "Trung tâm GDNN - GDTX Nho Quan",
        level: "Sơ cấp",
        district: "Nho Quan",
        program: "Kỹ thuật sản xuất kính & vật liệu",
        duration: "Khóa nghề 6 tháng"
      }
    ],
    demands: [
      { company: "Công ty TNHH Kính nổi Siêu trắng Chu Lai - CS Ninh Bình", quantity: 60 }
    ]
  },
  {
    id: "job-11",
    profession: "Kinh doanh & dịch vụ ô tô",
    recruitment_count: 35,
    skills: ["Tư vấn kỹ thuật ô tô", "Đàm phán hợp đồng", "Quy trình bàn giao xe 3S", "Kỹ năng số CRM"],
    training_schools: [
      {
        name: "Trường Cao đẳng Cơ giới Ninh Bình",
        level: "Cao đẳng",
        district: "TP. Tam Điệp",
        program: "Dịch vụ thương mại & Kỹ thuật ô tô",
        duration: "Khóa cao đẳng 3 năm"
      },
      {
        name: "Trường Trung cấp Kinh tế - Kỹ thuật Đổi Mới",
        level: "Trung cấp",
        district: "Yên Khánh",
        program: "Nghiệp vụ bán hàng & Marketing",
        duration: "Khóa trung cấp 2 năm"
      }
    ],
    demands: [
      { company: "Hyundai Ninh Bình (Đại lý ủy quyền chính hãng 3S)", quantity: 35 }
    ]
  },
  {
    id: "job-12",
    profession: "Kiểm tra chất lượng dệt may (QC/QA)",
    recruitment_count: 110,
    skills: ["Đọc thông số kỹ thuật", "Kiểm tra lỗi đường may AQL", "Kiểm định vải & phụ liệu", "Báo cáo QC"],
    training_schools: [
      {
        name: "Cơ sở GDNN C (mẫu)",
        level: "Sơ cấp",
        district: "Khu vực Nam Định",
        program: "May công nghiệp & KCS",
        duration: "Khóa sơ cấp 3 tháng"
      },
      {
        name: "Trung tâm GDNN - GDTX Kim Sơn",
        level: "Trung cấp · Sơ cấp",
        district: "Kim Sơn",
        program: "Quản trị chất lượng may mặc",
        duration: "Khóa đào tạo 1 năm"
      }
    ],
    demands: [
      { company: "Công ty TNHH May Xuất khẩu Kim Sơn", quantity: 110 }
    ]
  },
  {
    id: "job-13",
    profession: "Bảo dưỡng & Sửa chữa ô tô",
    recruitment_count: 85,
    skills: ["Chẩn đoán OBD-II", "Đại tu động cơ & hộp số", "Hệ thống phanh ABS", "Bảo dưỡng gầm máy"],
    training_schools: [
      {
        name: "Trường Cao đẳng Cơ giới Ninh Bình",
        level: "Cao đẳng · Trung cấp",
        district: "TP. Tam Điệp",
        program: "Công nghệ ô tô",
        duration: "Xưởng thực hành hiện đại"
      },
      {
        name: "Trường CĐ nghề số 20/BQP",
        level: "Trung cấp",
        district: "Phủ Lý",
        program: "Kỹ thuật sửa chữa gầm máy ô tô",
        duration: "Khóa 2 năm"
      }
    ],
    demands: [
      { company: "Xưởng Dịch vụ Sửa chữa Ô tô Thành Công Tam Điệp", quantity: 85 }
    ]
  },
  {
    id: "job-14",
    profession: "Logistics & Khai thác kho",
    recruitment_count: 75,
    skills: ["Vận hành xe nâng Forklift", "Phần mềm WMS", "Đóng gói xếp dỡ tiêu chuẩn", "Kiểm kê mã vạch"],
    training_schools: [
      {
        name: "Trường Cao đẳng Cơ giới Ninh Bình",
        level: "Trung cấp · Sơ cấp",
        district: "TP. Tam Điệp",
        program: "Nghiệp vụ kho vận & lái xe nâng",
        duration: "Cấp chứng chỉ lái xe nâng"
      },
      {
        name: "Trường Trung cấp Kinh tế - Kỹ thuật Đổi Mới",
        level: "Trung cấp",
        district: "Yên Khánh",
        program: "Logistics và quản lý chuỗi cung ứng",
        duration: "Khóa trung cấp 2 năm"
      }
    ],
    demands: [
      { company: "Công ty TNHH Logistics & Kho bãi Tân Cảng Ninh Bình", quantity: 75 }
    ]
  },
  {
    id: "job-15",
    profession: "Nuôi trồng thủy sản & Nông nghiệp sinh thái",
    recruitment_count: 40,
    skills: ["Kiểm soát pH nguồn nước", "Kỹ thuật phòng bệnh thủy sản", "Tỉa cành tạo tán cây cảnh", "Nông nghiệp tuần hoàn"],
    training_schools: [
      {
        name: "Trường Trung cấp Dân tộc Nội trú Ninh Bình",
        level: "Trung cấp",
        district: "Nho Quan",
        program: "Nuôi trồng thủy sản & Trồng trọt",
        duration: "Khóa 2 năm học bổng nội trú"
      },
      {
        name: "Trung tâm GDNN - GDTX Hoa Lư",
        level: "Sơ cấp",
        district: "Hoa Lư",
        program: "Kỹ thuật sinh thái cảnh quan du lịch",
        duration: "Khóa 3 tháng"
      }
    ],
    demands: [
      { company: "Khu Du lịch Sinh thái Thung Nham (Doanh nghiệp Ninh Bình)", quantity: 40 }
    ]
  },
  {
    id: "job-16",
    profession: "Cắt gọt kim loại",
    recruitment_count: 80,
    skills: ["CNC", "Đọc bản vẽ", "Đo kiểm"],
    training_schools: [
      {
        name: "Cơ sở GDNN B (mẫu)",
        level: "Trung cấp",
        district: "Khu vực Ninh Bình",
        program: "Cắt gọt kim loại",
        duration: "Bổ sung kỹ năng: 10 tuần"
      },
      {
        name: "Trường Cao đẳng Cơ điện Xây dựng Việt Xô",
        level: "Cao đẳng",
        district: "TP. Tam Điệp",
        program: "Cắt gọt kim loại CNC",
        duration: "Khóa 3 năm"
      }
    ],
    demands: [
      { company: "Doanh nghiệp Cơ khí B (mẫu)", quantity: 80 }
    ]
  },
  {
    id: "job-17",
    profession: "Tin học ứng dụng",
    recruitment_count: 50,
    skills: ["Excel", "Kỹ năng số"],
    training_schools: [
      {
        name: "Cơ sở GDNN C (mẫu)",
        level: "Dưới 3 tháng",
        district: "Khu vực Nam Định",
        program: "Tin học ứng dụng",
        duration: "Khóa kỹ năng: 4 tuần"
      },
      {
        name: "Trường Cao đẳng Cơ giới Ninh Bình",
        level: "Trung cấp",
        district: "TP. Tam Điệp",
        program: "Quản trị mạng máy tính",
        duration: "Khóa 2 năm"
      }
    ],
    demands: [
      { company: "Doanh nghiệp Dịch vụ E (mẫu)", quantity: 50 }
    ]
  },
  {
    id: "job-18",
    profession: "Công nghệ thông tin",
    recruitment_count: 55,
    skills: ["Lập trình Web/App", "Cơ sở dữ liệu SQL", "Giao diện UI/UX", "Quản trị hệ thống"],
    training_schools: [
      {
        name: "Trường Cao đẳng Cơ điện Xây dựng Việt Xô",
        level: "Cao đẳng",
        district: "TP. Tam Điệp",
        program: "Phần mềm & Ứng dụng CNTT",
        duration: "Khóa cao đẳng 3 năm"
      },
      {
        name: "Trường Trung cấp Kinh tế - Kỹ thuật Đổi Mới",
        level: "Trung cấp",
        district: "Yên Khánh",
        program: "Tin học ứng dụng văn phòng & Web",
        duration: "Khóa 2 năm"
      }
    ],
    demands: [
      { company: "Tổ hợp Sản xuất Ô tô Hyundai Thành Công Ninh Bình", quantity: 55 }
    ]
  }
];

const enrichedMap = new Map(enrichedData.map(d => [d.id, d]));

const updatedJobs = jobs.map(job => {
  const extra = enrichedMap.get(job.id);
  if (extra) {
    return {
      ...job,
      profession: extra.profession,
      recruitment_count: extra.recruitment_count,
      skills: extra.skills,
      training_schools: extra.training_schools,
      demands: extra.demands
    };
  }
  return job;
});

fs.writeFileSync(filePath, JSON.stringify(updatedJobs, null, 2), 'utf8');
console.log('Successfully enriched all 18 jobs in jobPostings.json');
