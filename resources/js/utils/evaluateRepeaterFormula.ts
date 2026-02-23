/**
 * Avalia uma fórmula do repeater com segurança.
 * Fórmula usa nomes de campos e operadores: +, -, *, /, ( ).
 * Ex.: "quantity * unitPrice - discount" com row = { quantity: 2, unitPrice: 10, discount: 1 } → 19
 */
export function evaluateRepeaterFormula(
  formula: string,
  row: Record<string, unknown>,
): number {
  if (!formula || typeof formula !== 'string') {
    return 0
  }
  const trimmed = formula.trim()
  if (!trimmed) return 0

  const safe = trimmed.replace(/\b([a-zA-Z_][a-zA-Z0-9_]*)\b/g, (_, name) => {
    const v = row[name]
    if (v === null || v === undefined || v === '') return '0'
    const n = Number(v)
    return Number.isNaN(n) ? '0' : String(n)
  })

  if (!/^[\d\s+\-*/().]+$/.test(safe)) {
    return 0
  }
  try {
    const result = new Function(`return (${safe})`)()
    const n = Number(result)
    return Number.isNaN(n) ? 0 : n
  } catch {
    return 0
  }
}
