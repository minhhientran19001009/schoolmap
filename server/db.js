import mysql from 'mysql2/promise'

export const pool = mysql.createPool({
  host: process.env.DB_HOST || '127.0.0.1',
  port: parseInt(process.env.DB_PORT || '3306'),
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB_NAME || 'schoolmap_ninhbinh',
  waitForConnections: true,
  connectionLimit: 10,
  queueLimit: 0
})

// Test connection
pool.getConnection()
  .then(conn => {
    console.log('Connected to XAMPP MySQL (schoolmap_ninhbinh) successfully!')
    conn.release()
  })
  .catch(err => {
    console.error('Failed to connect to XAMPP MySQL:', err.message)
  })
