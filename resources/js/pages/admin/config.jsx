/* eslint-disable no-console */
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardFooter } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Separator } from '@/components/ui/separator'
import { Switch } from '@/components/ui/switch'
import AppLayout from '@/layouts/app-layout'
import { Head, useForm } from '@inertiajs/react'
import { toast } from 'sonner'

const breadcrumbs = [
  {
    title: 'Home',
    href: '/',
  },
]

function Admin({ settings }) {
  const { data, setData, post } = useForm({
    show_video: settings.filter(item => item.field === 'show_video')[0].value,
    video_banner: null,
    dummy_bet_amount: settings.filter(item => item.field === 'dummy_bet_amount')[0].value,
  })

  const handleSave = (e) => {
    e.preventDefault()

    post('/admin/config', {
      onSuccess: () => {
        toast('Success!', {
          description: 'Configuration have been succesfully updated!',
          action: {
            label: 'OK',
            onClick: () => console.log('OK'),
          },
        })
      },
      onError: (errors) => {
        toast('Error!', {
          description: JSON.stringify(errors),
          action: {
            label: 'OK',
            onClick: () => console.log(errors),
          },
        })
      },
    })
  }

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Dashboard" />
      {/* <Banner /> */}
      <div className="space-y-6 p-10 pb-16 block">
        <div className="space-y-0.5">
          <h2 className="text-2xl font-bold tracking-tight">Settings</h2>
          <p className="text-muted-foreground">
            Manage site config and preferences.
          </p>
        </div>
        <Separator className="my-6" />
        <div className="flex flex-col space-y-8 lg:flex-row lg:space-x-12 lg:space-y-0">
          <form onSubmit={handleSave}>
            <Card>
              <CardContent className="space-y-4">
                <div className="space-y-1">
                  <Label htmlFor="name"></Label>
                  <div className="flex items-center space-x-2">
                    <Switch
                      defaultChecked={data.show_video === '1'}
                      onCheckedChange={val => setData('show_video', val)}
                      id="show_video"
                      name="show_video"
                    />
                    <Label htmlFor="show_video">Show Video</Label>
                  </div>
                </div>
                <div className="space-y-1">
                  <Label htmlFor="video_banner">Video Banner</Label>
                  <Input
                    id="video_banner"
                    type="file"
                    name="video_banner"
                    onChange={e => setData('video_banner', e.target.files[0])}
                  />
                </div>
                <div className="space-y-1">
                  <Label htmlFor="dummy_bet_amount">Dummy Bets Amount</Label>
                  <Input
                    id="dummy_bet_amount"
                    type="number"
                    name="dummy_bet_amount"
                    onChange={e => setData('dummy_bet_amount', e.target.value)}
                    defaultValue={data.dummy_bet_amount}
                  />
                </div>
              </CardContent>
              <CardFooter>
                <Button>Save changes</Button>
              </CardFooter>
            </Card>
          </form>
        </div>
      </div>
    </AppLayout>
  )
}

export default Admin
