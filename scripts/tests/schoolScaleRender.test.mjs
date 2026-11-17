import test from 'node:test'
import assert from 'node:assert/strict'
import { createServer } from 'vite'
import { createSSRApp } from 'vue'
import { renderToString } from '@vue/server-renderer'

test('school overview renders one scale table with source units and scrolling container', async () => {
  const server = await createServer({ server: { middlewareMode: true }, logLevel: 'silent' })
  try {
    const { default: SchoolDetailDrawer } = await server.ssrLoadModule('/src/components/school/SchoolDetailDrawer.vue')
    const school = {
      id: 'test-school', name: 'Trường kiểm thử', education_level: 'cao_dang',
      training_majors: [{ name: 'Hàn', annual_quota: 999 }],
      education_levels_catalog: [{ id: 'cao-dang', name: 'Cao đẳng' }, { id: 'trung-cap', name: 'Trung cấp' }],
      training_source_report: { programs: [{ name: 'Hàn', current_scale: { reported_total: 100, cao_dang: 20, trung_cap: 80, so_cap_va_khac: 0 } }] },
    }
    const html = await renderToString(createSSRApp(SchoolDetailDrawer, { school }))
    assert.equal((html.match(/Quy mô đào tạo/g) || []).length, 1)
    assert.match(html, /Tổng quy mô/)
    assert.match(html, /Người học/)
    assert.match(html, /Sơ cấp và khác/)
    assert.match(html, /overflow-x-auto/)
    assert.doesNotMatch(html, /Tổng chỉ tiêu|SV\/năm|>999</)
  } finally {
    await server.close()
  }
})
