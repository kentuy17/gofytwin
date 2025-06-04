import { createContext, useState } from 'react'

export const OperatorContext = createContext({
  fight: null,
  meron: null,
  wala: null,
  credits: 0,
  playerBet: {
    meron: 0,
    wala: 0,
  },
  status: null,
  lastResult: [],
  lastCall: false,
  setLastResult: () => {},
  setFight: () => { },
  setMeron: () => { },
  setWala: () => { },
  setPlayerBet: () => { },
  setStatus: () => { },
  setCredits: () => {},
})

function OperatorProvider({ children }) {
  const [fight, setFight] = useState('')
  const [meron, setMeron] = useState('')
  const [wala, setWala] = useState('')
  const [status, setStatus] = useState('')
  const [credits, setCredits] = useState(0)
  const [playerBet, setPlayerBet] = useState({
    meron: 0,
    wala: 0,
  })
  const [lastResult, setLastResult] = useState([])
  const [lastCall, setLastCall] = useState(false)

  return (
    <OperatorContext
      value={{
        fight,
        meron,
        wala,
        playerBet,
        status,
        credits,
        lastResult,
        lastCall,
        setFight,
        setMeron,
        setWala,
        setPlayerBet,
        setStatus,
        setCredits,
        setLastResult,
        setLastCall,
      }}
    >
      {children}
    </OperatorContext>
  )
}

export default OperatorProvider
