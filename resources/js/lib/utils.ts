/* eslint-disable no-console */
/* eslint-disable no-alert */
import type { ClassValue } from 'clsx'
import { clsx } from 'clsx'

import { twMerge } from 'tailwind-merge'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function isValidPhoneNumber(phoneNumber: any) {
  // const phoneRegex = /^\+?[1-9]\d{0,2}[-.\s]?\(?\d{1,4}\)?[-.\s]?\d{1,4}[-.\s]?\d{1,9}$/
  // return phoneRegex.test(phoneNumber)
  const num = removeSpaces(phoneNumber)
  if (num.startsWith('+639')) {
    return num.length === 13
  }

  if (num.startsWith('09')) {
    return num.length === 11
  }

  return false
}

export function removeSpaces(str: any) {
  return str.replace(/\s+/g, '')
}

export function formatMoney(value: any, peso: boolean = true) {
  Number.parseFloat(value)
  if (value === undefined) {
    console.log(value)

    alert('undefined')
    return
  }

  if (!peso) {
    return Number.isNaN(value)
      ? '0.00'
      : new Intl.NumberFormat('en-US', { maximumFractionDigits: 2 }).format(value)
  }

  const php = Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'PHP',
    maximumFractionDigits: 2,
  })

  return Number.isNaN(value)
    ? '0.00'
    : php.format(value)
}

export function sleep(ms: number): Promise<void> {
  return new Promise<void>(resolve => setTimeout(resolve, ms))
}
