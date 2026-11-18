import test from 'node:test'
import assert from 'node:assert/strict'
import { createServer } from 'vite'
import { createSSRApp } from 'vue'
import { renderToString } from '@vue/server-renderer'
import { readFileSync } from 'node:fs'
import { parse } from '@vue/compiler-sfc'

function templateElements(node) {
  return [node, ...(node.children || []).flatMap(templateElements)]
    .filter(item => item.type === 1)
}

test('school drawer transitions act directly on panel and backdrop; callers keep it mounted', () => {
  const source = readFileSync('src/components/school/SchoolDetailDrawer.vue', 'utf8')
  const { descriptor } = parse(source)
  const elements = templateElements(descriptor.template.ast)
  for (const [name, className] of [
    ['school-detail-slide', 'school-detail-panel'],
    ['school-detail-backdrop', 'backdrop-blur-[1.5px]'],
  ]) {
    const transition = elements.find(node => node.tag === 'Transition' && node.props.some(prop => prop.name === 'name' && prop.value?.content === name))
    assert.ok(transition, `missing ${name}`)
    const child = transition.children.find(node => node.type === 1)
    assert.ok(child.props.some(prop => prop.name === 'class' && prop.value?.content.split(/\s+/).includes(className)))
    assert.ok(child.props.some(prop => prop.type === 7 && prop.name === 'if'))
  }
  assert.match(descriptor.styles[0].content, /translate3d\(calc\(100% \+ 32px\), 0, 0\)/)
  assert.match(descriptor.styles[0].content, /transform 480ms/)
  assert.match(descriptor.styles[0].content, /prefers-reduced-motion: reduce/)
  for (const view of ['ClientMapView', 'ClientSchoolListView', 'ClientJobsView']) {
    const { descriptor: page } = parse(readFileSync(`src/views/client/${view}.vue`, 'utf8'))
    const drawer = templateElements(page.template.ast).find(node => node.tag === 'SchoolDetailDrawer')
    assert.ok(drawer)
    assert.ok(!drawer.props.some(prop => prop.type === 7 && prop.name === 'if'), `${view} must not unmount the drawer before it slides out`)
  }
})

test('school overview renders one scale table with source units and scrolling container', async () => {
  const server = await createServer({ server: { middlewareMode: true }, logLevel: 'silent' })
  try {
    const { default: SchoolDetailDrawer } = await server.ssrLoadModule('/src/components/school/SchoolDetailDrawer.vue')
    const school = {
      id: 'test-school', name: 'Trường kiểm thử', education_level: 'cao_dang',
      training_majors: [{ name: 'Hàn', annual_quota: 999 }],
      education_levels_catalog: [{ id: 'cao-dang', name: 'Cao đẳng' }, { id: 'trung-cap', name: 'Trung cấp' }],
      training_source_report: { programs: [{ name: 'Hàn', current_scale: { reported_total: 100, cao_dang: 20, trung_cap: 80, so_cap_va_khac: 0 } }] },
      admission_report: { summary: { total: 9999 }, programs: [{ name: 'CHỈ CÓ TRONG BÁO CÁO TUYỂN SINH', total: 9999 }] },
    }
    const html = await renderToString(createSSRApp(SchoolDetailDrawer, { school }))
    assert.equal((html.match(/Quy mô đào tạo/g) || []).length, 1)
    assert.match(html, /Tổng quy mô/)
    assert.match(html, /Người học/)
    assert.match(html, /Sơ cấp và khác/)
    assert.match(html, /overflow-x-auto/)
    assert.doesNotMatch(html, /Tổng chỉ tiêu|SV\/năm|>999</)
    assert.doesNotMatch(html, /Kết quả tuyển sinh theo báo cáo nguồn|Báo cáo tuyển sinh|CHỈ CÓ TRONG BÁO CÁO TUYỂN SINH|9\.999/)
    const closedHtml = await renderToString(createSSRApp(SchoolDetailDrawer, { school: null }))
    assert.doesNotMatch(closedHtml, /school-detail-panel|Quy mô đào tạo|school.name/)
  } finally {
    await server.close()
  }
})
