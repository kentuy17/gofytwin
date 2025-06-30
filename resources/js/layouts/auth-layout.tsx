import AuthLayoutTemplate from '@/layouts/auth/auth-simple-layout'

export default function AuthLayout({ children, title, description, login_promo_banner, ...props }: {
  children: React.ReactNode
  title: string
  description: string
  login_promo_banner: string
}) {
  return (
    <AuthLayoutTemplate title={title} description={description} login_promo_banner={login_promo_banner} {...props}>
      {children}
    </AuthLayoutTemplate>
  )
}
