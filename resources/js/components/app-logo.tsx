// import AppLogoIcon from './app-logo-icon'

function ManokLogo() {
  return <img src="/images/gamefowl-master-logo-official.png" className="max-w-full" />
}

export default function AppLogo() {
  return (
    <>
      <div className="bg-sidebar-primary text-sidebar-primary-foreground flex aspect-square size-8 items-center justify-center rounded-md">
        <ManokLogo />
      </div>
      <div className="ml-1 grid flex-1 text-left text-sm">
        <span className="mb-0.5 truncate leading-none font-semibold">GamefowlMaster</span>
      </div>
    </>
  )
}
