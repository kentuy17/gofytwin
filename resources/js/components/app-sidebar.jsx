// import type { NavItem } from '@/types'
import { NavFooter } from '@/components/nav-footer'
import { NavMain } from '@/components/nav-main'
import { NavUser } from '@/components/nav-user'
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { Link, usePage } from '@inertiajs/react'
import { BookOpen, Calendar, CoinsIcon, Folder, Joystick, LayoutGrid } from 'lucide-react'
import AppLogo from './app-logo'

const mainNavItems = [
  {
    title: 'Home',
    url: '/home',
    icon: LayoutGrid,
    level: 0,
  },
  {
    title: 'Cashin',
    url: '/player/deposit',
    icon: CoinsIcon,
    level: 0,
  },
  {
    title: 'Operator',
    url: '/operator',
    icon: Joystick,
    level: 1,
  },
  {
    title: 'Events',
    url: '/events',
    icon: Calendar,
    level: 1,
  },
  {
    title: 'My Bets',
    url: '/bets',
    icon: Calendar,
    level: 1,
  },
]

const footerNavItems = [
  {
    title: 'Admin Bets',
    url: '/admin/bets/list',
    icon: Folder,
  },
  {
    title: 'Users List',
    url: '/admin/users/list',
    icon: Folder,
  },
  {
    title: 'Verification OTP Codes',
    url: '/admin/users/otp/list',
    icon: Folder,
  },
  {
    title: 'Spam Players List',
    url: '/admin/users/spam',
    icon: Folder,
  },
  {
    title: 'Repository',
    url: 'https://github.com/laravel/react-starter-kit',
    icon: Folder,
  },
  {
    title: 'Documentation',
    url: 'https://laravel.com/docs/starter-kits',
    icon: BookOpen,
  },
]

export function AppSidebar() {
  const { auth } = usePage().props
  const filterNav = mainNavItems.filter(item => auth.user.user_level >= item.level)

  return (
    <Sidebar collapsible="icon" variant="inset">
      <SidebarHeader>
        <SidebarMenu>
          <SidebarMenuItem>
            <SidebarMenuButton size="lg" asChild>
              <Link href="/home" prefetch>
                <AppLogo />
              </Link>
            </SidebarMenuButton>
          </SidebarMenuItem>
        </SidebarMenu>
      </SidebarHeader>

      <SidebarContent>
        <NavMain items={filterNav} />
      </SidebarContent>

      <SidebarFooter>
        <NavFooter items={footerNavItems} className="mt-auto" />
        <NavUser />
      </SidebarFooter>
    </Sidebar>
  )
}
