import { usePage } from '@inertiajs/react'
import React, { createContext, useContext, useState } from 'react'

const { globals } = usePage().props

const OperatorContext = createContext({
  fight: globals.fight_no,
  meron: globals.total.meron,
  wala: globals.total.wala,
  playerBet: {
    meron: globals.player.meron,
    wala: globals.player.wala
  },
  setFight: () => { },
  setMeron: () => { },
  setWala: () => { },
  setPlayerBet: () => { },
})

const OperatorProvider = ({children}) => {
  const [fight, setFight] = useState(globals.fight_no)
  const [meron, setMeron] = useState(globals.total.meron)
  const [wala, setWala] = useState(globals.total.wala)
  const [playerBet, setPlayerBet] = useState({
    meron: globals.player.meron,
    wala: globals.player.wala
  })

  return (
    <OperatorContext.Provider
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
    </OperatorContext.Provider>
  )
}

export const useOperatorContext = () => useContext(OperatorContext)
// export const useStateContext = () => useContext(StateContext);