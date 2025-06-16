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
import { isValidPhoneNumber } from '@/lib/utils'
// import AuthBlock from '@/layouts/auth/auth-block'

import { Head, useForm, usePage } from '@inertiajs/react'
import { countries, getCountryFlagEmojiFromCountryCodeOrNameOrFlagEmoji } from 'country-codes-flags-phone-codes'
import { FacebookIcon, LoaderCircle } from 'lucide-react'

import { useEffect, useRef, useState } from 'react'
import 'react-phone-number-input/style.css'

export function PhoneInputCountry({ inputStr }) {
  const icon = getCountryFlagEmojiFromCountryCodeOrNameOrFlagEmoji('PH')
  const [activeIcon, setActiveIcon] = useState(icon)
  const [mobile_no, setmobile_no] = useState('')

  const handleInputChanged = (str) => {
    const { value } = str.target
    setmobile_no(value)
    const x = countries.findIndex(i => i.dialCode === value)

    if (x) {
      setActiveIcon(countries[x]?.flag)
    }
  }

  useEffect(() => {
    const newFlag = countries.find(country => country.dialCode === inputStr)?.flag
    return () => setActiveIcon(newFlag)
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

function LoginDialog({ site }) {
  const submitRef = useRef(null)
  const [submitDisabled, setSubmitDisabled] = useState(true)
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

    if (data.mobile_no.startsWith('+63')) {
      const tmp = data.mobile_no.replace('+63', '0')
      setData('mobile_no', tmp)
    }
    // console.log(site.csrf_token)
    grecaptcha.ready(() => {
      // do request for recaptcha token
      // response is promise with passed token
      grecaptcha.execute('6Le8DV4rAAAAAF-ouzsNUjLykUkSRauVLJ6gfmJm', { action: 'create_comment' }).then((token) => {
        fetch('/webrequest/captcha', {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': site.csrf_token,
          },
          body: JSON.stringify({
            data: { token },
          }),
        }).then((response) => {
          return Promise.all([response.json(), response])
        }).then(([responseData]) => {
          // handle data here

          if (responseData.success === true) {
            post('/otp/generate', {
              onFinish: () => reset('password'),
            })
          }
          else {
            alert('not working')
          }
        })
      })
    })
  }

  const handleMobileInput = (e) => {
    let val = e.target.value
    val = val.replace(/\D/g, '')
    if (val.length > 11)
      return
    setSubmitDisabled(val.length !== 11)
    setData('mobile_no', val)
  }

  const openInNewTab = (url) => {
    const newWindow = window.open(url, '_blank', 'noopener,noreferrer')
    if (newWindow)
      newWindow.opener = null
  }

  const handleFBClick = () => {
    return () => openInNewTab(import.meta.env.VITE_FB_SUP_LINK)
  }

  const handleMessengerClick = () => {
    return () => openInNewTab(import.meta.env.VITE_MESSENGER_SUP_LINK)
  }

  const inputGradient = {
    background: 'rgb(1, 1, 2)',
    // eslint-disable-next-line no-dupe-keys
    background: 'linear-gradient(90deg, rgba(1,1,2,1) 0%, rgba(34,34,49,1) 48%, rgba(84,84,84,1) 100%)',
    borderColor: 'rgba(84,84,84,1)',
    marginLeft: '16px',
    width: 'calc(100% - 32px)',
  }

  // eslint-disable-next-line no-console
  useEffect(() => console.log(null), [])

  return (
    <AlertDialog open>
      <AlertDialogTrigger></AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle className="text-yellow-500">Login / Register Here:</AlertDialogTitle>
          <AlertDialogDescription></AlertDialogDescription>
        </AlertDialogHeader>
        <form onSubmit={submit}>
          <Input
            onChange={handleMobileInput}
            value={data.mobile_no}
            placeholder="09XXXXXXXXXX"
            className="bg-gradient-to-l from-white to-zinc-900 to-75%"
            style={inputGradient}
            tabIndex={1}
          />
          <Button type="submit" ref={submitRef} className="hidden" tabIndex={4} disabled={processing}>
            Log in
          </Button>
          <div
            id="recaptcha"
            className="g-recaptcha"
            data-sitekey="6Le8DV4rAAAAAF-ouzsNUjLykUkSRauVLJ6gfmJm"
            data-callback="onSubmit"
            data-size="invisible"
          >
          </div>
        </form>
        <AlertDialogFooter className="sm:justify-center">
          {/* <AlertDialogCancel>Cancel</AlertDialogCancel> */}
          {/* <AlertDialogAction >Continue</AlertDialogAction> */}
          <AlertDialogAction
            onClick={() => submitRef.current.click()}
            type="submit"
            tabIndex={2}
            disabled={processing || submitDisabled}
            className="bg-[var(--color-gofyt)] font-bold uppercase"
          >
            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
            Submit
          </AlertDialogAction>
        </AlertDialogFooter>
        <span tabIndex={4} className="text-center text-yellow-500">Need help?</span>
        <div className="py-0 relative">
          <div className="absolute inset-0 flex items-center">
            <span className="w-full border-t" />
          </div>
          <div className="relative flex justify-center text-xs">
            <span className="bg-background px-2 text-muted-foreground" tabIndex={5}>
              Contact Us at our Facebook Page: Gamefowl Master
            </span>
          </div>
        </div>
        <div className="flex justify-center gap-4">
          <Button
            onClick={handleFBClick()}
            rel="noopener noreferrer"
            variant="default"
            style={{ backgroundColor: '#0866FF', cursor: 'pointer' }}
            size="icon"
            className="over-sized-btn rounded-full"
          >
            <FacebookIcon tabIndex={6} fill="var(--color-primary)" color="var(--color-primary)" className="over-sized-icon" />
          </Button>
          <Button
            onClick={handleMessengerClick()}
            className="bg-gradient-to-r from-[#00c6ff] to-[#0072ff] cursor-pointer over-sized-btn rounded-full"
            size="icon"
          >
            <svg className="over-sized-icon" tabIndex={7} role="img" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <title>Messenger</title>
              <path fill="var(--color-primary)" d="M12 0C5.24 0 0 4.952 0 11.64c0 3.499 1.434 6.521 3.769 8.61a.96.96 0 0 1 .323.683l.065 2.135a.96.96 0 0 0 1.347.85l2.381-1.053a.96.96 0 0 1 .641-.046A13 13 0 0 0 12 23.28c6.76 0 12-4.952 12-11.64S18.76 0 12 0m6.806 7.44c.522-.03.971.567.63 1.094l-4.178 6.457a.707.707 0 0 1-.977.208l-3.87-2.504a.44.44 0 0 0-.49.007l-4.363 3.01c-.637.438-1.415-.317-.995-.966l4.179-6.457a.706.706 0 0 1 .977-.21l3.87 2.505c.15.097.344.094.491-.007l4.362-3.008a.7.7 0 0 1 .364-.13" />
            </svg>
          </Button>

        </div>

      </AlertDialogContent>
    </AlertDialog>
  )
}

export default function Login({ site }) {
  const { flash } = usePage().props

  if (flash && flash.error) {
    alert(flash.error)
  }

  return (
    <AuthLayout>
      <Head title="Log in" />
      <LoginDialog site={site} />
    </AuthLayout>
  )
}
