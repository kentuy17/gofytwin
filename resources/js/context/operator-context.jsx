import { usePage } from '@inertiajs/react'
import { createContext, useContext, useState } from 'react'

// const OperatorContext = createContext()
// const { globals } = usePage().props

const OperatorContext = createContext({
  fight: null,
  meron: null,
  wala: null,
  playerBet: {
    meron: 0,
    wala: 0,
  },
  setFight: () => { },
  setMeron: () => { },
  setWala: () => { },
  setPlayerBet: () => { },
})

export function OperatorProvider({ children }) {
  const { globals } = usePage().props
  const [fight, setFight] = useState(globals.fight.fight_no)
  const [meron, setMeron] = useState(globals.total.meron)
  const [wala, setWala] = useState(globals.total.wala)
  const [playerBet, setPlayerBet] = useState({
    meron: globals.player.meron,
    wala: globals.player.wala,
  })

  return (
    <OperatorContext
      value={{
        fight,
        meron,
        wala,
        playerBet,
        setFight,
        setMeron,
        setWala,
        setPlayerBet,
      }}
    >
      {children}
    </OperatorContext>
  )
}

export const useOperatorContext = () => useContext(OperatorContext)
// export const useStateContext = () => useContext(StateContext);
