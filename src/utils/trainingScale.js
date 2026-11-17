const SOURCE_LEVELS = [
  { id: 'cao_dang', name: 'Cao đẳng' },
  { id: 'trung_cap', name: 'Trung cấp' },
  { id: 'so_cap_va_khac', name: 'Sơ cấp và khác' },
]
const ALL_SCALE_LEVELS = [{ id: 'dai_hoc', name: 'Đại học' }, ...SOURCE_LEVELS, { id: 'gdtx', name: 'GDTX - GDNN' }]

export function scaleNumber(value) {
  if (value === null || value === undefined || value === '') return null
  const number = Number(value)
  return Number.isFinite(number) && number >= 0 ? number : null
}

export function trainingScaleTable(report, catalog = [], fallbackMajors = []) {
  const levels = new Map(catalog.map(level => [String(level.id).replaceAll('-', '_'), {
    id: String(level.id).replaceAll('-', '_'), name: level.name,
  }]))
  for (const level of SOURCE_LEVELS) levels.set(level.id, level)
  const source = Array.isArray(report?.programs) ? report.programs : []
  const codes = new Map(fallbackMajors.map(major => [String(major.name || '').trim().toLocaleLowerCase('vi'), major.code || major.major_code || '']))
  const hasReport = source.length > 0
  for (const level of ALL_SCALE_LEVELS) {
    if (!levels.has(level.id) && source.some(program => scaleNumber(program.current_scale?.[level.id]) !== null)) levels.set(level.id, level)
  }
  const rows = hasReport ? source.map((program, index) => {
    const data = program.current_scale || {}
    const values = Object.fromEntries(ALL_SCALE_LEVELS.map(level => [level.id,
      scaleNumber(data[level.id] ?? (SOURCE_LEVELS.some(item => item.id === level.id) ? null : 0)),
    ]))
    const known = ALL_SCALE_LEVELS.filter(level => scaleNumber(data[level.id]) !== null).map(level => values[level.id])
    const sum = known.reduce((total, value) => total + value, 0)
    const reported = scaleNumber(data.reported_total)
    const conflict = reported !== null && known.length > 0 && reported < sum
    const total = conflict ? null : reported ?? (known.length > 0 ? sum : scaleNumber(data.clean_total))
    const unallocated = total !== null && known.length > 0 ? Math.max(total - sum, 0) : scaleNumber(data.unallocated)
    const code = program.source_codes?.join(' / ') || codes.get(String(program.name).trim().toLocaleLowerCase('vi')) || ''
    return { key: `${program.name}-${index}`, name: program.name, code, values, total, unallocated, conflict }
  }) : Array.from(new Set(fallbackMajors.map(major => String(major.name || '').trim()).filter(Boolean)))
    .map(name => ({ key: name, name, code: codes.get(name.toLocaleLowerCase('vi')) || '', values: {}, total: null, unallocated: null, conflict: false }))
  const sumField = getter => {
    const values = rows.map(getter).filter(value => value !== null && value !== undefined)
    return values.length > 0 ? values.reduce((sum, value) => sum + value, 0) : null
  }
  const hasUnallocated = rows.some(row => row.unallocated > 0)
  const columns = [...levels.values()]
  if (hasUnallocated) columns.push({ id: 'unallocated', name: 'Chưa phân bổ' })
  for (const row of rows) {
    for (const column of columns) {
      if (column.id === 'unallocated') row.values[column.id] = row.unallocated
      else if (!ALL_SCALE_LEVELS.some(level => level.id === column.id)) row.values[column.id] = hasReport ? 0 : null
    }
  }
  const summary = report?.summary?.current_scale?.source_total_row || {}
  const summaryTotal = scaleNumber(summary.reported_total)
  const summaryValues = ALL_SCALE_LEVELS.map(level => scaleNumber(summary[level.id])).filter(value => value !== null)
  const summaryConflict = summaryTotal !== null && summaryValues.length > 0 && summaryTotal < summaryValues.reduce((sum, value) => sum + value, 0)
  const hasConflict = summaryConflict || rows.some(row => row.conflict)
  return {
    hasReport, rows, columns, hasConflict,
    total: hasConflict ? null : sumField(row => row.total),
    totals: Object.fromEntries(columns.map(level => [level.id, sumField(row => row.values[level.id])])),
  }
}
