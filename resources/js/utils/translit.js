// URL-friendly transliteration (RU -> EN) with hyphens between words
// Usage: import { translitSlug } from '@/utils/translit';
//   translitSlug('Иоганн Себастьян Бах') => 'iohann-sebastyan-bah'

const MAP = {
  а: 'a', б: 'b', в: 'v', г: 'g', д: 'd', е: 'e', ё: 'yo', ж: 'zh', з: 'z', и: 'i',
  й: 'y', к: 'k', л: 'l', м: 'm', н: 'n', о: 'o', п: 'p', р: 'r', с: 's', т: 't',
  у: 'u', ф: 'f', х: 'h', ц: 'c', ч: 'ch', ш: 'sh', щ: 'sch', ъ: '', ы: 'y',
  ь: '', э: 'e', ю: 'yu', я: 'ya',
};

function translitChar(ch) {
  const lower = ch.toLowerCase();
  if (MAP.hasOwnProperty(lower)) {
    return MAP[lower];
  }
  // Keep ascii letters and digits; everything else will be normalized later
  if (/^[a-z0-9]$/i.test(ch)) return ch.toLowerCase();
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

export default translitSlug;
