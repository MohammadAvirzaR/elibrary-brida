export type LicenseType = 'ARR' | 'CC_BY' | 'CC_BY_SA' | 'CC_BY_NC' | 'CC_BY_ND'

export const LICENSE_OPTIONS: Array<{ value: LicenseType; label: string; description: string }> = [
  { value: 'ARR', label: 'All Rights Reserved', description: 'Hak cipta dilindungi penuh. Penggunaan ulang dibatasi.' },
  { value: 'CC_BY', label: 'CC BY', description: 'Boleh dibagikan/adaptasi dengan atribusi.' },
  { value: 'CC_BY_SA', label: 'CC BY-SA', description: 'Boleh dibagikan/adaptasi dengan atribusi dan lisensi serupa.' },
  { value: 'CC_BY_NC', label: 'CC BY-NC', description: 'Boleh dibagikan/adaptasi non-komersial dengan atribusi.' },
  { value: 'CC_BY_ND', label: 'CC BY-ND', description: 'Boleh dibagikan dengan atribusi tanpa turunan.' },
]

export const isCreativeCommons = (licenseType?: string | null): boolean => {
  return typeof licenseType === 'string' && licenseType.startsWith('CC')
}

export const getLicenseLabel = (licenseType?: string | null): string => {
  const found = LICENSE_OPTIONS.find((item) => item.value === licenseType)
  return found?.label || 'All Rights Reserved'
}

export const getLicenseDescription = (licenseType?: string | null): string => {
  const found = LICENSE_OPTIONS.find((item) => item.value === licenseType)
  const fallback = LICENSE_OPTIONS.find((item) => item.value === 'ARR')?.description
    ?? 'Hak cipta dilindungi penuh. Penggunaan ulang dibatasi.'
  return found?.description ?? fallback
}

export const generateAttributionText = (title: string, author: string, licenseType?: string | null, version = '4.0'): string => {
  const label = getLicenseLabel(licenseType)
  return `${title} oleh ${author}, dilisensikan di bawah ${label} ${version}.`
}
