/* eslint-disable no-console */
/* eslint-disable no-undef */
// import TextLink from '@/components/text-link'

import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import AuthLayout from '@/layouts/auth-layout'
import { cn } from '@/lib/utils'
import { Head, useForm } from '@inertiajs/react'
import { countries, getCountryFlagEmojiFromCountryCodeOrNameOrFlagEmoji } from 'country-codes-flags-phone-codes'
import { LoaderCircle } from 'lucide-react'
import { useEffect, useState } from 'react'

import PhoneInput from 'react-phone-number-input'
import 'react-phone-number-input/style.css'

// export function PhoneInput({ data }) {
//   console.log(countries)
//   console.log(data)

//   const phFlag = getCountryFlagEmojiFromCountryCodeOrNameOrFlagEmoji('PH')

//   const [open, setOpen] = useState(false)
//   const [value, setValue] = useState(phFlag)

//   const handleOnSelectId = ($ret) => {
//     setValue($ret)
//   }

//   useEffect(() => {
//     console.log(value, 'flag')
//   }, [value])

//   return (
//     <Popover open={open} onOpenChange={setOpen}>
//       <PopoverTrigger asChild>
//         <Button
//           variant="outline"
//           role="combobox"
//           aria-expanded={open}
//           className="w-[50px] justify-between"
//         >
//           {value
//             ? countries.find(country => country.dialCode === value)?.flag
//             : value}
//           {value}
//           <ChevronsUpDown className="opacity-50" />
//         </Button>
//       </PopoverTrigger>
//       <PopoverContent className="w-[200px] p-0">
//         <Command>
//           <CommandInput placeholder="Search framework..." className="h-9" />
//           <CommandList>
//             <CommandEmpty>No framework found.</CommandEmpty>
//             <CommandGroup>
//               {countries.map(country => (
//                 <CommandItem
//                   key={country.code}
//                   // value={country.code}

//                   // setValue

//                   onSelect={(currentValue) => {
//                     setValue(currentValue === country.flag ? '' : currentValue)
//                     setOpen(false)
//                     handleOnSelectId(currentValue)
//                   }}
//                 >
//                   {`${country.flag} ${country.name}`}
//                   <Check
//                     className={cn(
//                       'ml-auto',
//                       value === country.flag ? 'opacity-100' : 'opacity-0',
//                     )}
//                   />
//                 </CommandItem>
//               ))}
//             </CommandGroup>
//           </CommandList>
//         </Command>
//       </PopoverContent>
//     </Popover>
//   )
// }

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

export default function Login({ status }) {
  const { data, setData, post, processing, reset } = useForm({
    phoneNum: '',
    remember: false,
  })

  const submit = (e) => {
    e.preventDefault()
    post(route('login'), {
      onFinish: () => reset('password'),
    })
  }

  const handleInputChanged = (e) => {
    setData('phoneNum', e)
    // console.log(e)
  }

  return (
    <AuthLayout>
      <Head title="Log in" />

      <form className="flex flex-col gap-6" onSubmit={submit}>
        <div className="grid gap-6">
          <PhoneInput
            // international
            defaultCountry="PH"
            placeholder="Enter phone number"
            value={data.phoneNum}
            onChange={handleInputChanged}
            className={cn(
              'border-input file:text-foreground placeholder:text-muted-foreground selection:bg-primary selection:text-primary-foreground flex h-9 w-full min-w-0 rounded-md border bg-transparent px-3 py-1 text-base shadow-xs transition-[color,box-shadow] outline-none file:inline-flex file:h-7 file:border-0 file:bg-transparent file:text-sm file:font-medium disabled:pointer-events-none disabled:cursor-not-allowed disabled:opacity-50 md:text-sm',
              'focus-visible:border-ring focus-visible:ring-ring/50 focus-visible:ring-[3px]',
              'aria-invalid:ring-destructive/20 dark:aria-invalid:ring-destructive/40 aria-invalid:border-destructive',
            )}
          />
          <div className="flex items-center space-x-3">
            <Checkbox id="remember" name="remember" checked={data.remember} onClick={() => setData('remember', !data.remember)} tabIndex={3} />
            <Label htmlFor="remember">Remember me</Label>
          </div>

          <Button type="submit" className="mt-4 w-full" tabIndex={4} disabled={processing}>
            {processing && <LoaderCircle className="h-4 w-4 animate-spin" />}
            Log in
          </Button>
        </div>
      </form>

      {status && <div className="mb-4 text-center text-sm font-medium text-green-600">{status}</div>}
    </AuthLayout>
  )
}
