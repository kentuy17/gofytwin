import type { User } from '@/types'
import { DropdownMenuGroup, DropdownMenuItem, DropdownMenuLabel, DropdownMenuSeparator } from '@/components/ui/dropdown-menu'
import { UserInfo } from '@/components/user-info'
import { Link, router } from '@inertiajs/react'
import { LogOut, Settings } from 'lucide-react'
import { useCallback } from 'react'

interface UserMenuContentProps {
  user: User
}

export function UserMenuContent({ user }: UserMenuContentProps) {
  const cleanup = useCallback(() => {
    // Remove pointer-events style from body...
    document.body.style.removeProperty('pointer-events')
  }, [])

  const handleSettingsClicked = (e: any) => {
    e.preventDefault()
    router.visit('/admin/config')

    return cleanup
  }

  // const handleLogout = () => {
  //   localStorage.setItem('isAuthenticated', 'false')
  // }

  return (
    <form method="post">
      <DropdownMenuLabel className="p-0 font-normal">
        <div className="flex items-center gap-2 px-1 py-1.5 text-left text-sm">
          <UserInfo user={user} showEmail />
        </div>
      </DropdownMenuLabel>
      <DropdownMenuSeparator />
      <DropdownMenuGroup>
        <DropdownMenuItem asChild>
          <Link className="block w-full" href="#" as="button" type="button" prefetch onClick={handleSettingsClicked}>
            <Settings className="mr-2" />
            Settingss
          </Link>
        </DropdownMenuItem>
      </DropdownMenuGroup>
      <DropdownMenuSeparator />
      <DropdownMenuItem asChild>

        <Link className="block w-full" method="post" href={route('logout')} as="button" type="button" onClick={cleanup}>
          <LogOut className="mr-2" />
          Log out
        </Link>

      </DropdownMenuItem>
    </form>
  )
}
