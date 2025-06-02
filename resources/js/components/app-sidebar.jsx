// import type { NavItem } from '@/types'
// import { NavFooter } from '@/components/nav-footer'
import { NavMain } from '@/components/nav-main'
import { NavUser } from '@/components/nav-user'
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { Link, usePage } from '@inertiajs/react'
import { Calendar, CoinsIcon, Folder, Joystick, LayoutGrid, Settings, UserCog2Icon } from 'lucide-react'
import AppLogo from './app-logo'
import { NavAdmin } from './nav-admin'

const navItems = [
  {
    name: 'Player',
    level: 0,
    items: [
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
        title: 'Cashout',
        url: '/player/cashout',
        icon: CoinsIcon,
        level: 0,
      },
      {
        title: 'Bets',
        url: '/bets',
        icon: CoinsIcon,
        level: 0,
      },
      {
        title: 'My Cashin List',
        url: '/player/my/cash-in',
        icon: CoinsIcon,
        level: 0,
      },
      {
        title: 'My Cashout List',
        url: '/player/my/cash-out',
        icon: CoinsIcon,
        level: 0,
      },
    ],
  },
  {
    name: 'Operator',
    level: 1,
    items: [
      {
        title: 'Controller',
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
        title: 'Settings',
        url: '/admin/config',
        icon: Settings,
        level: 1,
      },
    ],
  },
  // {
  //   title: 'My Bets',
  //   url: '/bets',
  //   icon: Calendar,
  //   level: 1,
  // },
]

const adminNavItems = [
  {
    title: 'Admin',
    url: '#',
    icon: UserCog2Icon,
    isActive: true,
    items: [
      {
        title: 'Total Gross',
        url: '/admin/total-gross',
        icon: Folder,
      },
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
    ],
  },
]

// const footerNavItems = [
//   {
//     title: 'Total Gross',
//     url: '/admin/total-gross',
//     icon: Folder,
//   },
//   {
//     title: 'Admin Bets',
//     url: '/admin/bets/list',
//     icon: Folder,
//   },
//   {
//     title: 'Users List',
//     url: '/admin/users/list',
//     icon: Folder,
//   },
//   {
//     title: 'Verification OTP Codes',
//     url: '/admin/users/otp/list',
//     icon: Folder,
//   },
//   {
//     title: 'Spam Players List',
//     url: '/admin/users/spam',
//     icon: Folder,
//   },
//   // {
//   //   title: 'Repository',
//   //   url: 'https://github.com/laravel/react-starter-kit',
//   //   icon: Folder,
//   // },
//   // {
//   //   title: 'Documentation',
//   //   url: 'https://laravel.com/docs/starter-kits',
//   //   icon: BookOpen,
//   // },
// ]

export function AppSidebar() {
  const { auth } = usePage().props
  const userLevel = Number.parseInt(auth.user.user_level)

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
        {navItems.map(navItem => (
          (userLevel === navItem.level || userLevel >= 2)
          && <NavMain key={navItem.level} group={navItem.name} items={navItem.items} />
        ))}
        {userLevel >= 2 && <NavAdmin items={adminNavItems} />}
      </SidebarContent>

      <SidebarFooter>
        {/* <NavFooter items={footerNavItems} className="mt-auto" /> */}
        <NavUser />
      </SidebarFooter>
    </Sidebar>
  )
}
