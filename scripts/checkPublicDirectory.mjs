import assert from 'node:assert/strict'
import fs from 'node:fs/promises'

// Smoke test the actual Vue services against the local Laravel API.
// Run after starting Laravel: node scripts/checkPublicDirectory.mjs
const origin = process.env.DIRECTORY_API_ORIGIN || 'http://127.0.0.1:8000'
const originalFetch = globalThis.fetch
globalThis.fetch = (url, options) => originalFetch(new URL(url, origin), options)
const vueUrl = import.meta.resolve('vue')
async function importService(file, replacements = {}) {
  let source = await fs.readFile(new URL('../src/services/' + file, import.meta.url), 'utf8')
  source = source.replaceAll("from 'vue'", `from '${vueUrl}'`)
  for (const [specifier, url] of Object.entries(replacements)) source = source.replaceAll(`from '${specifier}'`, `from '${url}'`)
  const url = 'data:text/javascript;base64,' + Buffer.from(source).toString('base64')
  return { url, module: await import(url) }
}
const directory = await importService('publicDirectoryService.js')
const jobs = await importService('jobService.js', { './publicDirectoryService': directory.url })
const enterprises = await importService('enterpriseService.js', { './publicDirectoryService': directory.url, './jobService': jobs.url })
assert.equal(jobs.module.jobService.getAll().length, 0)
await directory.module.loadPublicDirectory(true)
assert.equal(directory.module.publicDirectoryError.value, '')
const data = directory.module.publicDirectory.value
assert.ok(data.jobs.length > 0, 'API cần có tin việc làm để kiểm tra')
assert.equal(jobs.module.jobService.getAll().length, data.jobs.length)
assert.equal(enterprises.module.enterpriseService.getAll().length, data.employers.length)
for (const job of jobs.module.jobService.getAll()) {
  assert.ok(jobs.module.jobService.getById(job.id))
  const enterprise = enterprises.module.enterpriseService.getByCompanyName(job.company)
  assert.ok(enterprise, `Không tìm thấy doanh nghiệp của ${job.id}`)
  assert.ok(enterprise.jobs.some(item => item.id === job.id))
  assert.ok(jobs.module.jobService.filterJobs({ search: job.title }).some(item => item.id === job.id))
}
assert.equal(jobs.module.jobService.getCareerFields().reduce((sum, field) => sum + field.job_count, 0), data.jobs.length)
console.log(`Đạt: ${data.jobs.length} tin, ${data.employers.length} doanh nghiệp; tải API, tìm kiếm, chi tiết, liên kết và nhóm lĩnh vực.`)
