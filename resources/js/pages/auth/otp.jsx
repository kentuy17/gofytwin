/* eslint-disable no-console */
/* eslint-disable no-undef */
/* eslint-disable no-alert */
// import TextLink from '@/components/text-link'

import {
  AlertDialog,
  // AlertDialogAction,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from '@/components/ui/alert-dialog'

import { Button } from '@/components/ui/button'

import { Input } from '@/components/ui/input'
import {
  InputOTP,
  InputOTPGroup,
  // InputOTPSeparator,
  InputOTPSlot,
} from '@/components/ui/input-otp'

import AuthLayout from '@/layouts/auth-layout'
// import AuthBlock from '@/layouts/auth/auth-block'

// import { cn } from '@/lib/utils'
import { Head, useForm } from '@inertiajs/react'
import { countries, getCountryFlagEmojiFromCountryCodeOrNameOrFlagEmoji } from 'country-codes-flags-phone-codes'
// import { LoaderCircle } from 'lucide-react'
import { phone } from 'phone'

import { useEffect, useState } from 'react'
// import PhoneInput from 'react-phone-number-input'
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
  // const submitRef = useRef(null)
  const { data, setData, post, reset } = useForm({
    otp: '',
  })

  const submit = (e) => {
    e.preventDefault()
    const { isValid } = phone(data.mobile_no)

    if (!isValid) {
      alert('Invalid format')
      return
    }

    post(route('otp.generate'), {
      onFinish: () => reset('password'),
    })
  }

  return (
    <AlertDialog open className="flex flex-col items-center">
      <AlertDialogTrigger>Open</AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader className="lg:text-center">
          <AlertDialogTitle>OTP VERIFICATION</AlertDialogTitle>
        </AlertDialogHeader>
        <AlertDialogDescription></AlertDialogDescription>
        <form onSubmit={submit}>
          <div className="space-y-2 flex flex-col items-center">
            <InputOTP
              maxLength={4}
              value={data.otp}
              onChange={value => setData('otp', value)}
            >
              <InputOTPGroup>
                <InputOTPSlot index={0} />
                <InputOTPSlot index={1} />
                <InputOTPSlot index={2} />
                <InputOTPSlot index={3} />
              </InputOTPGroup>

            </InputOTP>
            <div className="text-center text-sm">
              {data.otp === ''
                ? (
                    <>Enter your one-time password.</>
                  )
                : (
                    <>
                      You entered:
                      {data.otp}
                    </>
                  )}
            </div>
          </div>
        </form>
        <AlertDialogFooter>
          {/* <AlertDialogCancel>Cancel</AlertDialogCancel> */}
          {/* <AlertDialogAction >Continue</AlertDialogAction> */}
          {/* <AlertDialogAction
            onClick={() => submitRef.current.click()}
            type="submit"
            tabIndex={4}
            disabled={processing}
          >
            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
            Continue
          </AlertDialogAction> */}
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  )
}

export default function Otp({ status, site }) {
  console.log(site)
  return (
    <AuthLayout>
      <Head title="Log in" />
      <LoginDialog />
      {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}
    </AuthLayout>
  )
}
