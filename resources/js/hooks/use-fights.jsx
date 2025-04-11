import { OperatorContext } from '@/context/operator-context'
import { useContext } from 'react'

export function useCleanup() {
  const { setPlayerBet, setMeron, setWala } = useContext(OperatorContext)
  setPlayerBet({
    meron: 0,
    wala: 0,
  })

  setMeron(0)
  setWala(0)
}
