/* eslint-disable no-console */
/* eslint-disable no-undef */
/* eslint-disable no-alert */
// import TextLink from '@/components/text-link'

import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'

import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'

import AuthLayout from '@/layouts/auth-layout'
// import AuthBlock from '@/layouts/auth/auth-block'

import { cn } from '@/lib/utils'
import { Head, useForm } from '@inertiajs/react'
import { countries, getCountryFlagEmojiFromCountryCodeOrNameOrFlagEmoji } from 'country-codes-flags-phone-codes'
import { LoaderCircle } from 'lucide-react'
import { phone } from 'phone'

import { useEffect, useRef, useState } from 'react'
import PhoneInput from 'react-phone-number-input'
import 'react-phone-number-input/style.css'

export function PhoneInputCountry({ inputStr }) {
  const icon = getCountryFlagEmojiFromCountryCodeOrNameOrFlagEmoji('PH')
  const [activeIcon, setActiveIcon] = useState(icon)
  const [phoneNum, setPhoneNum] = useState('')
  // console.log(data)

  const handleInputChanged = (str) => {
    const { value } = str.target
    console.log(value, 'blogger')
    setPhoneNum(value)
    const x = countries.findIndex(i => i.dialCode === value)

    if (x) {
      setActiveIcon(countries[x]?.flag)
    }
  }

  useEffect(() => {
    const newFlag = countries.find(country => country.dialCode === inputStr)?.flag
    setActiveIcon(newFlag)
  }, [inputStr])

  return (
    <div className="flex">
      <div dir="ltr">
        <Button variant="secondary" type="button" className="cursor-pointer rounded-none p-2 text-xl ">{activeIcon || icon}</Button>
      </div>
      <div dir="ltr">
        <Input placeholder="6811604" onKeyDown={handleInputChanged} className="rounded-none" />
        <span>{phoneNum}</span>
      </div>
    </div>
  )
}

function LoginDialog() {
  const submitRef = useRef(null)
  const { data, setData, post, processing, reset } = useForm({
    phoneNum: '',
    remember: false,
  })

  const submit = (e) => {
    e.preventDefault()
    const { isValid } = phone(data.phoneNum)

    if (!isValid) {
      alert('Invalid format')
      return
    }

    post(route('login'), {
      onFinish: () => reset('password'),
    })
  }

  return (
    <AlertDialog open>
      <AlertDialogTrigger>Open</AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Login</AlertDialogTitle>
          <AlertDialogDescription></AlertDialogDescription>
        </AlertDialogHeader>
        <form onSubmit={submit}>
          <PhoneInput
            // international
            defaultCountry="PH"
            placeholder="Enter phone number"
            value={data.phoneNum}
            onChange={e => setData('phoneNum', e)}
            className={cn(
              'border-input file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
              'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
              'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
            )}
          />
          <Button type="submit" ref={submitRef} className="hidden" tabIndex={4} disabled={processing}>
            Log in
          </Button>
        </form>
        <AlertDialogFooter>
          {/* <AlertDialogCancel>Cancel</AlertDialogCancel> */}
          {/* <AlertDialogAction >Continue</AlertDialogAction> */}
          <AlertDialogAction
            onClick={() => submitRef.current.click()}
            type="submit"
            tabIndex={4}
            disabled={processing}
          >
            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
            Continue
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  )
}

export default function Login({ status }) {
  return (
    <AuthLayout>
      <Head title="Log in" />
      <LoginDialog />
      {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}
    </AuthLayout>
  )
}
