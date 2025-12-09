// URL-friendly transliteration (RU -> EN) with hyphens between words
// Usage: import { translitSlug } from '@/utils/translit';
//   translitSlug('Иоганн Себастьян Бах') => 'iohann-sebastyan-bah'

const MAP = {
  а: 'a', б: 'b', в: 'v', г: 'g', д: 'd', е: 'e', ё: 'yo', ж: 'zh', з: 'z', и: 'i',
  й: 'y', к: 'k', л: 'l', м: 'm', н: 'n', о: 'o', п: 'p', р: 'r', с: 's', т: 't',
  у: 'u', ф: 'f', х: 'h', ц: 'c', ч: 'ch', ш: 'sh', щ: 'sch', ъ: '', ы: 'y',
  ь: '', э: 'e', ю: 'yu', я: 'ya',
};

// Extended mapping for special Latin letters that don't reduce cleanly by diacritics
// (explicit to ensure predictable outputs across environments)
const EXT_LATIN_MAP = {
  // Germanic
  'ß': 'ss', 'ẞ': 'ss',
  // Nordic
  'æ': 'ae', 'Æ': 'ae', 'œ': 'oe', 'Œ': 'oe', 'ø': 'o', 'Ø': 'o', 'å': 'a', 'Å': 'a',
  // Slavic and others
  'ł': 'l', 'Ł': 'l', 'đ': 'd', 'Đ': 'd', 'ð': 'd', 'Ð': 'd', 'þ': 'th', 'Þ': 'th',
  // Common with cedilla/ogonek etc. (covered by diacritic strip but map just in case)
  'ç': 'c', 'Ç': 'c', 'ñ': 'n', 'Ñ': 'n',
};

function translitChar(ch) {
  const lower = ch.toLowerCase();
  if (MAP.hasOwnProperty(lower)) {
    return MAP[lower];
  }
  // Explicit special-latin handling
  if (EXT_LATIN_MAP.hasOwnProperty(ch)) {
    return EXT_LATIN_MAP[ch];
  }
  if (EXT_LATIN_MAP.hasOwnProperty(lower)) {
    return EXT_LATIN_MAP[lower];
  }
  // Keep ascii letters and digits; everything else will be normalized later
  if (/^[a-z0-9]$/i.test(ch)) return ch.toLowerCase();
  // Try to strip diacritics for latin letters (e.g., é -> e, ä -> a)
  // Using Unicode NFD to separate base letters and combining marks
  // Fallback to removing standard combining range if \p{Diacritic} unsupported
  let base = ch.normalize ? ch.normalize('NFD') : ch;
  // Remove combining marks U+0300 - U+036F
  base = base.replace(/[\u0300-\u036f]/g, '');
  // After stripping, allow ascii letters/digits
  if (/^[a-z0-9]$/i.test(base)) return base.toLowerCase();
  // For any other char (including spaces and punctuation), return a hyphen marker
  return '-';
}

export function translitSlug(input) {
  if (input == null) return '';
  const str = String(input);

  // Map characters
  let out = '';
  for (const ch of str) {
    out += translitChar(ch);
  }

  // Replace any sequence of non [a-z0-9] already mapped as '-' with a single '-'
  out = out
    .replace(/[^a-z0-9]+/g, '-') // safety: in case some chars slipped through
    .replace(/-+/g, '-') // collapse
    .replace(/^-+|-+$/g, '') // trim
    .toLowerCase();

  return out;
}

// Slug generator for dictionary terms which preserves non-ASCII letters (e.g. à, á)
// and only normalizes whitespace and ASCII punctuation to hyphens.
export function dictionarySlug(input) {
  if (input == null) return '';
  let out = String(input).trim().toLowerCase();

  // Replace whitespace and ASCII punctuation (except '-') with single hyphens
  // ASCII ranges used:
  //   0x21-0x2C, 0x2E-0x2F, 0x3A-0x40, 0x5B-0x60, 0x7B-0x7E
  out = out
    .replace(/[\s\x21-\x2C\x2E-\x2F\x3A-\x40\x5B-\x60\x7B-\x7E]+/g, '-')
    .replace(/-+/g, '-')
    .replace(/^-+|-+$/g, '');

  return out;
}

export default translitSlug;
