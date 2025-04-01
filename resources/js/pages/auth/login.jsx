/* eslint-disable no-console */

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
import { isValidPhoneNumber } from '@/lib/utils'
// import AuthBlock from '@/layouts/auth/auth-block'

import { Head, useForm, usePage } from '@inertiajs/react'
import { countries, getCountryFlagEmojiFromCountryCodeOrNameOrFlagEmoji } from 'country-codes-flags-phone-codes'
import { LoaderCircle } from 'lucide-react'

import { useEffect, useRef, useState } from 'react'
import 'react-phone-number-input/style.css'

export function PhoneInputCountry({ inputStr }) {
  const icon = getCountryFlagEmojiFromCountryCodeOrNameOrFlagEmoji('PH')
  const [activeIcon, setActiveIcon] = useState(icon)
  const [mobile_no, setmobile_no] = useState('')
  // console.log(data)

  const handleInputChanged = (str) => {
    const { value } = str.target
    console.log(value, 'blogger')
    setmobile_no(value)
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
        <span>{mobile_no}</span>
      </div>
    </div>
  )
}

export function InputOtpDialog() {
  return (
    <div>InputOtpDialog</div>
  )
}

function LoginDialog() {
  const submitRef = useRef(null)
  const { data, setData, post, processing, reset } = useForm({
    mobile_no: '',
    remember: true,
  })

  const submit = (e) => {
    e.preventDefault()

    const isValid = isValidPhoneNumber(data.mobile_no)
    if (!isValid) {
      alert('Invalid format')
      return
    }

    post('/otp/generate', {
      onFinish: () => reset('password'),
    })
  }

  const handleMobileInput = (e) => {
    const val = e.target.value
    setData('mobile_no', val)
  }

  return (
    <AlertDialog open>
      <AlertDialogTrigger></AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Login / Register Here:</AlertDialogTitle>
          <AlertDialogDescription></AlertDialogDescription>
        </AlertDialogHeader>
        <form onSubmit={submit}>
          {/* <PhoneInput
            // international
            defaultCountry="PH"
            placeholder="Enter phone number"
            value={data.mobile_no}
            onChange={e => setData('mobile_no', e)}
            focusInputOnCountrySelection
            className={cn(
              'border-input file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
              'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
              'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
            )}
          /> */}
          <Input
            onChange={handleMobileInput}
            value={data.mobile_no}
            placeholder="09XX XXX XXXXX"
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
            Submit
          </AlertDialogAction>
        </AlertDialogFooter>
        <div className="relative">
          <div className="absolute inset-0 flex items-center">
            <span className="w-full border-t" />
          </div>
          <div className="relative flex justify-center text-xs uppercase">
            <span className="bg-background px-2 text-muted-foreground">
              Contact Support
            </span>
          </div>
        </div>
        <div className="flex justify-center gap-4">
          <Button variant="outline" size="icon">
            <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <title>Messenger</title>
              <path fill="#0866FF" d="M12 0C5.24 0 0 4.952 0 11.64c0 3.499 1.434 6.521 3.769 8.61a.96.96 0 0 1 .323.683l.065 2.135a.96.96 0 0 0 1.347.85l2.381-1.053a.96.96 0 0 1 .641-.046A13 13 0 0 0 12 23.28c6.76 0 12-4.952 12-11.64S18.76 0 12 0m6.806 7.44c.522-.03.971.567.63 1.094l-4.178 6.457a.707.707 0 0 1-.977.208l-3.87-2.504a.44.44 0 0 0-.49.007l-4.363 3.01c-.637.438-1.415-.317-.995-.966l4.179-6.457a.706.706 0 0 1 .977-.21l3.87 2.505c.15.097.344.094.491-.007l4.362-3.008a.7.7 0 0 1 .364-.13" />
            </svg>
          </Button>
          <Button variant="outline" size="icon">
            <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <title>Facebook</title>
              <path d="M9.101 23.691v-7.98H6.627v-3.667h2.474v-1.58c0-4.085 1.848-5.978 5.858-5.978.401 0 .955.042 1.468.103a8.68 8.68 0 0 1 1.141.195v3.325a8.623 8.623 0 0 0-.653-.036 26.805 26.805 0 0 0-.733-.009c-.707 0-1.259.096-1.675.309a1.686 1.686 0 0 0-.679.622c-.258.42-.374.995-.374 1.752v1.297h3.919l-.386 2.103-.287 1.564h-3.246v8.245C19.396 23.238 24 18.179 24 12.044c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.628 3.874 10.35 9.101 11.647Z" />
            </svg>
          </Button>
          {/* <Button variant="outline" size="icon">
            <svg role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <title>Gmail</title>
              <path fill="#EA4335" d="M24 5.457v13.909c0 .904-.732 1.636-1.636 1.636h-3.819V11.73L12 16.64l-6.545-4.91v9.273H1.636A1.636 1.636 0 0 1 0 19.366V5.457c0-2.023 2.309-3.178 3.927-1.964L5.455 4.64 12 9.548l6.545-4.91 1.528-1.145C21.69 2.28 24 3.434 24 5.457z" />
            </svg>
          </Button> */}

        </div>

      </AlertDialogContent>
    </AlertDialog>
  )
}

export default function Login({ site }) {
  const { flash } = usePage().props
  console.log(flash)

  console.log(site)

  if (flash && flash.error) {
    alert(flash.error)
  }

  return (
    <AuthLayout>
      <Head title="Log in" />
      <LoginDialog />

    </AuthLayout>
  )
}
