/**
 * Converte valor de campo (incl. string formatada como moeda) em número.
 * Ex.: "1.234,56" (BRL) → 1234.56, "1,234.56" (US) → 1234.56
 */
export function parseNumberFromField(value: unknown): number {
  if (value === null || value === undefined || value === '') return 0
  if (typeof value === 'number' && !Number.isNaN(value)) return value
  const s = String(value).trim()
  if (!s) return 0
  let n = Number(s)
  if (!Number.isNaN(n)) return n
  const noSpaces = s.replace(/\s/g, '')
  const lastComma = noSpaces.lastIndexOf(',')
  const lastDot = noSpaces.lastIndexOf('.')
  if (lastComma > lastDot) {
    const brl = noSpaces.replace(/\./g, '').replace(',', '.')
    n = Number(brl)
  } else if (lastDot > lastComma) {
    const us = noSpaces.replace(/,/g, '')
    n = Number(us)
  }
  return Number.isNaN(n) ? 0 : n
}

/**
 * Avalia uma fórmula do repeater com segurança.
 * Fórmula usa nomes de campos e operadores: +, -, *, /, ( ).
 * Ex.: "qty * unitPrice - discount" com row = { qty: 2, unitPrice: 10, discount: 1 } → 19
 * Valores de campo podem ser número ou string formatada (ex.: moeda).
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
    const n = parseNumberFromField(v)
    return String(n)
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
