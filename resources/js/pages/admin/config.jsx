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

function formatData(data) {
  const formatted = {}
  data.forEach(({ field, value }) => formatted[field] = value)
  return formatted
}

function Admin({ settings }) {
  const { data, setData, post } = useForm(formatData(settings))

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
      <div className="space-y-6 p-4 pb-16 block">
        <div className="space-y-0.5">
          <h2 className="text-2xl font-bold tracking-tight">Settings</h2>
          <p className="text-muted-foreground">
            Manage site config and preferences.
          </p>
        </div>
        <Separator className="my-6" />
        <div className="flex flex-col space-y-8 lg:space-x-12 lg:space-y-0">
          <form onSubmit={handleSave}>
            <Card>
              <CardContent className="space-y-4 lg:columns-1 lg:max-w-1/2">
                <div className="space-y-1">
                  <Label htmlFor="name"></Label>
                  <div className="flex items-center space-x-2">
                    <Switch
                      defaultChecked={data.pg_gcash === '1'}
                      onCheckedChange={val => setData('pg_gcash', val)}
                      id="pg_gcash"
                      name="pg_gcash"
                    />
                    <Label htmlFor="pg_gcash">Enable Gcash</Label>
                  </div>
                </div>
                <div className="space-y-1">
                  <Label htmlFor="name"></Label>
                  <div className="flex items-center space-x-2">
                    <Switch
                      defaultChecked={data.pg_maya === '1'}
                      onCheckedChange={val => setData('pg_maya', val)}
                      id="pg_maya"
                      name="pg_maya"
                    />
                    <Label htmlFor="pg_maya">Enable PayMaya</Label>
                  </div>
                </div>
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
                <div className="space-y-1">
                  <Label htmlFor="dummy_bet_amount">Min Bet</Label>
                  <Input
                    id="min_bet"
                    type="number"
                    name="min_bet"
                    onChange={e => setData('min_bet', e.target.value)}
                    defaultValue={data.min_bet}
                  />
                </div>
                <div className="space-y-1">
                  <Label htmlFor="dummy_bet_amount">Max Bet</Label>
                  <Input
                    id="max_bet"
                    type="number"
                    name="max_bet"
                    onChange={e => setData('max_bet', e.target.value)}
                    defaultValue={data.max_bet}
                  />
                </div>
                <div className="space-y-1">
                  <Label htmlFor="no_credits_banner">No Credits Banner</Label>
                  <Input
                    id="no_credits_banner"
                    type="file"
                    name="no_credits_banner"
                    onChange={e => setData('no_credits_banner', e.target.files[0])}
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
