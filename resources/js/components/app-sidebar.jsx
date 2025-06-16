// import type { NavItem } from '@/types'
// import { NavFooter } from '@/components/nav-footer'
import { NavMain } from '@/components/nav-main'
import { NavUser } from '@/components/nav-user'
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { Link, usePage } from '@inertiajs/react'
import { Calendar, ChartAreaIcon, CoinsIcon, Folder, HandCoinsIcon, Joystick, LayoutGrid, Settings, UserCog2Icon } from 'lucide-react'
import AppLogo from './app-logo'
import { NavAdmin } from './nav-admin'
import { BanknoteArrowDown } from './svg/BanknoteArrowDown'
import { BanknoteArrowUp } from './svg/BanknoteArrowUp'

const navItems = [
  {
    name: 'Account',
    level: 0,
    items: [
      {
        title: 'Home',
        url: '/home',
        icon: LayoutGrid,
        level: 0,
      },
      {
        title: 'Cash-In',
        url: '/player/deposit',
        icon: CoinsIcon,
        level: 0,
      },
      {
        title: 'Cash Out',
        url: '/player/cashout',
        icon: HandCoinsIcon,
        level: 0,
      },
    ],
  },
  {
    name: 'History',
    level: 0,
    items: [
      {
        title: 'Deposits',
        url: '/player/my/cash-in',
        icon: BanknoteArrowUp,
        level: 0,
      },
      {
        title: 'Withdrawals',
        url: '/player/my/cash-out',
        icon: BanknoteArrowDown,
        level: 0,
      },
      {
        title: 'Bets',
        url: '/bets',
        icon: ChartAreaIcon,
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
          && <NavMain key={`${navItem.level}-${navItem.name}`} group={navItem.name} items={navItem.items} />
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
