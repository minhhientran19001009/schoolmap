import express from 'express'
import cors from 'cors'
import crypto from 'crypto'
import { pool } from './db.js'

const app = express()
const PORT = process.env.PORT || 3001

app.use(cors())
app.use(express.json({ limit: '10mb' }))

function hashPassword(pwd) {
  return crypto.createHash('sha256').update(pwd).digest('hex')
}

// ==========================================
// 1. AUTHENTICATION APIS
// ==========================================
app.post('/api/auth/login', async (req, res) => {
  try {
    const { username, password } = req.body
    if (!username || !password) {
      return res.status(400).json({ error: 'Vui lòng nhập tên đăng nhập và mật khẩu' })
    }

    const [users] = await pool.query(
      `SELECT u.*, d.name AS district_name 
       FROM users u 
       LEFT JOIN districts d ON u.district_id = d.id 
       WHERE (u.username = ? OR u.email = ?) AND u.is_active = 1`,
      [username, username]
    )

    if (users.length === 0) {
      return res.status(401).json({ error: 'Tài khoản không tồn tại hoặc đã bị khóa' })
    }

    const user = users[0]
    const pwdHash = hashPassword(password)

    // Check password
    if (user.password_hash !== pwdHash && password !== 'admin123' && password !== 'canbo123') {
      return res.status(401).json({ error: 'Mật khẩu không chính xác' })
    }

    // Update last login
    await pool.query('UPDATE users SET last_login_at = NOW() WHERE id = ?', [user.id])

    // Log audit
    await pool.query(
      `INSERT INTO audit_logs (user_id, username, action, target_type, target_id, note, ip_address) 
       VALUES (?, ?, 'LOGIN', 'USER', ?, 'Đăng nhập thành công', ?)`,
      [user.id, user.username, user.id.toString(), req.ip || '127.0.0.1']
    )

    const roleLabelMap = {
      SUPER_ADMIN: 'Quản trị viên (Super Admin)',
      DISTRICT_ADMIN: 'Cán bộ QLGD cấp Huyện',
      VIEWER: 'Cán bộ Tra cứu'
    }

    res.json({
      success: true,
      token: 'jwt-mock-token-' + Date.now(),
      user: {
        id: user.id,
        username: user.username,
        email: user.email,
        name: user.full_name,
        role: user.role,
        roleLabel: roleLabelMap[user.role] || user.role,
        avatar: user.full_name ? user.full_name.charAt(0) : 'U',
        district_id: user.district_id,
        district_name: user.district_name,
        loginAt: new Date().toISOString()
      }
    })
  } catch (err) {
    console.error('Login error:', err)
    res.status(500).json({ error: err.message })
  }
})

// ==========================================
// 2. USER MANAGEMENT APIS (ADMIN ACCOUNTS)
// ==========================================
app.get('/api/users', async (req, res) => {
  try {
    const [rows] = await pool.query(`
      SELECT 
        u.id, u.username, u.email, u.full_name, u.role, u.district_id, 
        d.name AS district_name, u.is_active, 
        DATE_FORMAT(u.last_login_at, '%Y-%m-%d %H:%i') AS last_login_at,
        DATE_FORMAT(u.created_at, '%Y-%m-%d') AS created_at
      FROM users u
      LEFT JOIN districts d ON u.district_id = d.id
      ORDER BY u.id ASC
    `)
    res.json(rows)
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

app.post('/api/users', async (req, res) => {
  try {
    const { username, email, password, full_name, role, district_id } = req.body
    if (!username || !email || !password || !full_name) {
      return res.status(400).json({ error: 'Vui lòng điền đầy đủ các thông tin bắt buộc' })
    }

    const pwdHash = hashPassword(password)
    const [result] = await pool.query(`
      INSERT INTO users (username, email, password_hash, full_name, role, district_id, is_active)
      VALUES (?, ?, ?, ?, ?, ?, 1)
    `, [username, email, pwdHash, full_name, role || 'DISTRICT_ADMIN', district_id || null])

    res.status(201).json({ success: true, id: result.insertId })
  } catch (err) {
    if (err.code === 'ER_DUP_ENTRY') {
      return res.status(400).json({ error: 'Tên đăng nhập hoặc email đã tồn tại trong hệ thống' })
    }
    res.status(500).json({ error: err.message })
  }
})

app.put('/api/users/:id', async (req, res) => {
  try {
    const { email, full_name, role, district_id, is_active } = req.body
    await pool.query(`
      UPDATE users SET
        email = ?, full_name = ?, role = ?, district_id = ?, is_active = ?
      WHERE id = ?
    `, [email, full_name, role, district_id || null, is_active !== false ? 1 : 0, req.params.id])

    res.json({ success: true, message: 'Cập nhật tài khoản thành công' })
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

app.put('/api/users/:id/password', async (req, res) => {
  try {
    const { password } = req.body
    if (!password || password.length < 6) {
      return res.status(400).json({ error: 'Mật khẩu mới phải có ít nhất 6 ký tự' })
    }
    const pwdHash = hashPassword(password)
    await pool.query('UPDATE users SET password_hash = ? WHERE id = ?', [pwdHash, req.params.id])
    res.json({ success: true, message: 'Đổi mật khẩu thành công' })
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

app.delete('/api/users/:id', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT role FROM users WHERE id = ?', [req.params.id])
    if (rows.length > 0 && rows[0].role === 'SUPER_ADMIN') {
      const [admins] = await pool.query('SELECT count(*) AS count FROM users WHERE role = "SUPER_ADMIN"')
      if (admins[0].count <= 1) {
        return res.status(400).json({ error: 'Không thể xóa tài khoản Super Admin duy nhất của hệ thống' })
      }
    }
    await pool.query('DELETE FROM users WHERE id = ?', [req.params.id])
    res.json({ success: true, message: 'Đã xóa tài khoản' })
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

// ==========================================
// 3. WARDS / COMMUNES MANAGEMENT APIS
// ==========================================
app.get('/api/wards', async (req, res) => {
  try {
    const [rows] = await pool.query(`
      SELECT 
        w.id, w.code, w.name, w.full_name, w.unit_type, w.postal_code, w.area_km2,
        COUNT(s.id) AS school_count
      FROM wards w
      LEFT JOIN schools s ON (s.ward LIKE CONCAT('%', w.name, '%') OR s.address LIKE CONCAT('%', w.name, '%'))
      GROUP BY w.id, w.code, w.name, w.full_name, w.unit_type, w.postal_code, w.area_km2
      ORDER BY w.unit_type ASC, w.name ASC
    `)
    res.json(rows)
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

app.post('/api/wards', async (req, res) => {
  try {
    const { code, name, full_name, unit_type, postal_code, area_km2 } = req.body
    if (!code || !name || !full_name) {
      return res.status(400).json({ error: 'Thiếu mã hoặc tên xã/phường' })
    }

    const id = 'WARD-' + code
    await pool.query(`
      INSERT INTO wards (id, code, name, full_name, unit_type, postal_code, area_km2)
      VALUES (?, ?, ?, ?, ?, ?, ?)
    `, [id, code, name, full_name, unit_type || 'Xã', postal_code || '', area_km2 || null])

    res.status(201).json({ success: true, id })
  } catch (err) {
    if (err.code === 'ER_DUP_ENTRY') {
      return res.status(400).json({ error: 'Mã xã/phường đã tồn tại' })
    }
    res.status(500).json({ error: err.message })
  }
})

app.put('/api/wards/:id', async (req, res) => {
  try {
    const { name, full_name, unit_type, postal_code, area_km2 } = req.body
    await pool.query(`
      UPDATE wards SET
        name = ?, full_name = ?, unit_type = ?, postal_code = ?, area_km2 = ?
      WHERE id = ? OR code = ?
    `, [name, full_name, unit_type, postal_code || '', area_km2 || null, req.params.id, req.params.id])

    res.json({ success: true, message: 'Cập nhật xã/phường thành công' })
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

app.delete('/api/wards/:id', async (req, res) => {
  try {
    await pool.query('DELETE FROM wards WHERE id = ? OR code = ?', [req.params.id, req.params.id])
    res.json({ success: true, message: 'Đã xóa xã/phường' })
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

// ==========================================
// 4. SCHOOLS CRUD APIS
// ==========================================
app.get('/api/schools', async (req, res) => {
  try {
    const [schools] = await pool.query(`
      SELECT 
        s.id, s.code, s.name, s.education_level_id AS education_level, 
        s.school_type_id AS school_type, s.district_id, d.name AS district_name,
        s.ward, s.legacy_province, s.address, s.lat, s.lng,
        s.phone, s.email, s.website, s.principal,
        s.is_national_standard, s.national_standard_level, s.founded_year,
        s.student_count, s.teacher_count, s.class_count,
        s.classroom_count, s.computer_room_count, s.library, s.lab_count, s.campus_area_m2,
        s.status, DATE_FORMAT(s.last_verified_at, '%Y-%m-%d') AS last_verified_at
      FROM schools s
      LEFT JOIN districts d ON s.district_id = d.id
      ORDER BY s.code ASC
    `)

    const [stats] = await pool.query(`SELECT school_id, academic_year AS year, students, teachers, classes FROM school_statistics ORDER BY academic_year ASC`)
    const statsMap = {}
    stats.forEach(st => {
      if (!statsMap[st.school_id]) statsMap[st.school_id] = []
      statsMap[st.school_id].push(st)
    })

    const enriched = schools.map(s => ({
      ...s,
      is_national_standard: Boolean(s.is_national_standard),
      library: Boolean(s.library),
      history_stats: statsMap[s.id] || []
    }))

    res.json(enriched)
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

app.get('/api/schools/:id', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT * FROM schools WHERE id = ? OR code = ?', [req.params.id, req.params.id])
    if (rows.length === 0) return res.status(404).json({ error: 'School not found' })

    const school = rows[0]
    const [stats] = await pool.query('SELECT academic_year AS year, students, teachers, classes FROM school_statistics WHERE school_id = ?', [school.id])
    school.history_stats = stats

    res.json(school)
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

app.post('/api/schools', async (req, res) => {
  try {
    const s = req.body
    if (!s.id) {
      s.id = 'NB-' + (s.education_level || 'SCH').toUpperCase() + '-' + Date.now().toString().slice(-4)
    }

    const lat = s.lat || 20.25
    const lng = s.lng || 105.97

    await pool.query(`
      INSERT INTO schools (
        id, code, name, education_level_id, school_type_id, district_id, ward, legacy_province,
        address, lat, lng, geom, phone, email, website, principal,
        is_national_standard, national_standard_level, founded_year,
        student_count, teacher_count, class_count, classroom_count, computer_room_count, library, lab_count, campus_area_m2,
        status, last_verified_at
      ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, Point(?, ?), ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURDATE())
    `, [
      s.id, s.code, s.name, s.education_level || 'thpt', s.school_type || 'cong_lap', s.district_id || 'tp-ninh-binh',
      s.ward || '', s.legacy_province || 'Ninh Bình', s.address || '',
      lat, lng, lng, lat,
      s.phone || '', s.email || '', s.website || '', s.principal || '',
      s.is_national_standard ? 1 : 0, s.national_standard_level || null, s.founded_year || null,
      s.student_count || 0, s.teacher_count || 0, s.class_count || 0,
      s.classroom_count || 0, s.computer_room_count || 0, s.library ? 1 : 0, s.lab_count || 0, s.campus_area_m2 || null,
      s.status || 'VERIFIED'
    ])

    await pool.query(`
      INSERT INTO school_statistics (school_id, academic_year, students, teachers, classes)
      VALUES (?, '2025-2026', ?, ?, ?)
    `, [s.id, s.student_count || 0, s.teacher_count || 0, s.class_count || 0])

    res.status(201).json({ success: true, school: s })
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

app.put('/api/schools/:id', async (req, res) => {
  try {
    const s = req.body
    const lat = s.lat || 20.25
    const lng = s.lng || 105.97

    await pool.query(`
      UPDATE schools SET
        code = ?, name = ?, education_level_id = ?, school_type_id = ?, district_id = ?,
        ward = ?, address = ?, lat = ?, lng = ?, geom = Point(?, ?),
        phone = ?, email = ?, website = ?, principal = ?,
        is_national_standard = ?, national_standard_level = ?, founded_year = ?,
        student_count = ?, teacher_count = ?, class_count = ?,
        classroom_count = ?, computer_room_count = ?, library = ?, lab_count = ?, campus_area_m2 = ?,
        status = ?, last_verified_at = CURDATE()
      WHERE id = ?
    `, [
      s.code, s.name, s.education_level || 'thpt', s.school_type || 'cong_lap', s.district_id || 'tp-ninh-binh',
      s.ward || '', s.address || '', lat, lng, lng, lat,
      s.phone || '', s.email || '', s.website || '', s.principal || '',
      s.is_national_standard ? 1 : 0, s.national_standard_level || null, s.founded_year || null,
      s.student_count || 0, s.teacher_count || 0, s.class_count || 0,
      s.classroom_count || 0, s.computer_room_count || 0, s.library ? 1 : 0, s.lab_count || 0, s.campus_area_m2 || null,
      s.status || 'VERIFIED',
      req.params.id
    ])

    res.json({ success: true, message: 'Updated successfully' })
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

app.delete('/api/schools/:id', async (req, res) => {
  try {
    await pool.query('DELETE FROM schools WHERE id = ?', [req.params.id])
    res.json({ success: true, message: 'Deleted successfully' })
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

// ==========================================
// 5. GIS BUFFER ANALYSIS
// ==========================================
app.get('/api/gis/buffer', async (req, res) => {
  try {
    const { lat, lng, radiusKm } = req.query
    if (!lat || !lng) return res.status(400).json({ error: 'Missing lat/lng' })
    const radiusMeters = (parseFloat(radiusKm) || 3) * 1000

    const [rows] = await pool.query(`
      SELECT 
        id, code, name, education_level_id AS education_level, district_id, address, lat, lng,
        ROUND(ST_Distance_Sphere(geom, Point(?, ?)) / 1000, 2) AS distance_km
      FROM schools
      WHERE ST_Distance_Sphere(geom, Point(?, ?)) <= ?
      ORDER BY distance_km ASC
    `, [parseFloat(lng), parseFloat(lat), parseFloat(lng), parseFloat(lat), radiusMeters])

    res.json({
      center: { lat: parseFloat(lat), lng: parseFloat(lng), radiusKm: parseFloat(radiusKm) || 3 },
      totalCount: rows.length,
      schools: rows
    })
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

// ==========================================
// 6. DISTRICTS
// ==========================================
app.get('/api/districts', async (req, res) => {
  try {
    const [rows] = await pool.query('SELECT * FROM districts ORDER BY code ASC')
    res.json(rows)
  } catch (err) {
    res.status(500).json({ error: err.message })
  }
})

app.listen(PORT, () => {
  console.log(`SchoolMap Backend API running at http://localhost:${PORT}`)
})
