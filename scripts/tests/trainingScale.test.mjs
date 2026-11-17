import test from 'node:test'
import assert from 'node:assert/strict'
import { trainingScaleTable } from '../../src/utils/trainingScale.js'

test('scale uses source levels and never uses annual quotas as fallback', () => {
  const empty = trainingScaleTable(null, [{ id: 'dai-hoc', name: 'Đại học' }], [{ name: 'Hàn', annual_quota: 999 }])
  assert.equal(empty.total, null)
  assert.equal(empty.rows[0].total, null)
  const table = trainingScaleTable({ programs: [{ name: 'Hàn', current_scale: {
    reported_total: 100, cao_dang: 20, trung_cap: 80, so_cap_va_khac: 0,
  } }] }, [{ id: 'dai-hoc', name: 'Đại học' }])
  assert.equal(table.total, 100)
  assert.equal(table.rows[0].values.dai_hoc, 0)
  assert.equal(table.totals.cao_dang, 20)
})

test('blank and zero remain distinct; unallocated figures are not assigned to a level', () => {
  const table = trainingScaleTable({ programs: [{ name: 'Lái xe', current_scale: {
    reported_total: 500, cao_dang: null, trung_cap: 35, so_cap_va_khac: null,
  } }] })
  assert.equal(table.rows[0].values.cao_dang, null)
  assert.equal(table.rows[0].values.unallocated, 465)
  assert.equal(table.total, 500)
})

test('contradictory source total is not silently replaced by computed total', () => {
  const table = trainingScaleTable({ programs: [{ name: 'Lái xe', current_scale: {
    reported_total: 0, clean_total: 4700, cao_dang: 0, trung_cap: 0, so_cap_va_khac: 4700,
  } }] })
  assert.equal(table.rows[0].total, null)
  assert.equal(table.total, null)
  assert.equal(table.hasConflict, true)
  assert.equal(table.totals.so_cap_va_khac, 4700)
})

test('school-level conflict is retained even if individual totals can be calculated', () => {
  const table = trainingScaleTable({
    programs: [{ name: 'Lái xe', current_scale: { reported_total: null, so_cap_va_khac: 4700 } }],
    summary: { current_scale: { source_total_row: { reported_total: 0, trung_cap: 450, so_cap_va_khac: 4700 } } },
  })
  assert.equal(table.rows[0].total, 4700)
  assert.equal(table.hasConflict, true)
  assert.equal(table.total, null)
})

test('university and continuing education figures are kept in their own levels', () => {
  const table = trainingScaleTable({ programs: [{ name: 'Tin học', current_scale: {
    reported_total: 170, dai_hoc: 100, gdtx: 70, cao_dang: 0, trung_cap: 0, so_cap_va_khac: 0,
  } }] })
  assert.equal(table.total, 170)
  assert.equal(table.totals.dai_hoc, 100)
  assert.equal(table.totals.gdtx, 70)
  assert.equal(table.totals.cao_dang, 0)
})

test('a program without figures does not become zero because of optional default columns', () => {
  const table = trainingScaleTable({ programs: [{ name: 'Tin học', current_scale: {} }] })
  assert.equal(table.rows[0].total, null)
  assert.equal(table.total, null)
})
