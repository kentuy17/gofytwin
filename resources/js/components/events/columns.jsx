/* eslint-disable no-alert */
/* eslint-disable no-console */
import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  // DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

import { Input } from '@/components/ui/input'
import { OperatorContext } from '@/context/operator-context'
import { router } from '@inertiajs/react'
import { MoreHorizontal } from 'lucide-react'
import { useContext, useRef, useState } from 'react'
import { DataTableColumnHeader } from './data-table-column-header'

export function SetFightDialog({ children, fight, eventId }) {
  const [open, setOpen] = useState(false)
  const submitRef = useRef(null)
  const inputRef = useRef(null)

  const handleSumbit = async (e) => {
    console.log(e)
    router.post('/event/update-fight', {
      event_id: eventId,
      fight: inputRef.current.value,
    }, {
      onError: errors => alert(errors.msg),
      onSuccess: (page) => {
        console.log(page)
        setOpen(false)
      },
    })
  }

  const handleOnEnter = (e) => {
    if (e.key === 'Enter') {
      submitRef.current.click()
    }
  }

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        {children}
      </DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Input Fight Number</DialogTitle>
        </DialogHeader>
        <div className="grid gap-4 py-4">
          <Input ref={inputRef} type="number" defaultValue={fight} onKeyDown={handleOnEnter} />
        </div>
        <DialogFooter>
          <Button type="submit" ref={submitRef} onClick={handleSumbit}>
            Submit
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}

function ActionsComponents({ event }) {
  const { status } = useContext(OperatorContext)

  if (!status || typeof status === 'undefined') {
    router.visit('/operator')
  }

  return (
    <DropdownMenu>
      <DropdownMenuTrigger asChild>
        <Button variant="ghost" className="h-8 w-8 p-0">
          <span className="sr-only">Open menu</span>
          <MoreHorizontal className="h-4 w-4" />
        </Button>
      </DropdownMenuTrigger>
      <DropdownMenuContent align="end">
        {/* <DropdownMenuLabel>Actions</DropdownMenuLabel> */}
        {/* <DropdownMenuItem
              onClick={() => navigator.clipboard.writeText(event.id)}
            >
              Copy event ID
            </DropdownMenuItem> */}
        <DropdownMenuItem asChild>
          <SetFightDialog eventId={event.id} fight={event.fight}>
            <Button size="sm" variant="ghost" className="w-full flex flex-start">Set Fight#</Button>
          </SetFightDialog>
        </DropdownMenuItem>
        <DropdownMenuSeparator />
        <DropdownMenuItem onClick={() => router.post(`/event/activate/${event.id}`)} disabled={event.status === 'ACTIVE'}>Activate</DropdownMenuItem>
        <DropdownMenuItem>Edit</DropdownMenuItem>
        <DropdownMenuItem>Delete</DropdownMenuItem>
      </DropdownMenuContent>
    </DropdownMenu>
  )
}

export const columns = [
  {
    id: 'select',
    header: ({ table }) => (
      <Checkbox
        checked={
          table.getIsAllPageRowsSelected()
          || (table.getIsSomePageRowsSelected() && 'indeterminate')
        }
        onCheckedChange={value => table.toggleAllPageRowsSelected(!!value)}
        aria-label="Select all"
        className="hidden"
      />
    ),
    cell: ({ row }) => (
      <Checkbox
        checked={row.getIsSelected()}
        onCheckedChange={value => row.toggleSelected(!!value)}
        aria-label="Select row"
        className="hidden"
      />
    ),
    enableSorting: false,
    enableHiding: false,
  },
  {
    accessorKey: 'id',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="#" />
    },
  },
  {
    accessorKey: 'name',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Name" />
    },
  },
  {
    accessorKey: 'status',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Status" />
    },
  },
  // {
  //   accessorKey: 'amount',
  //   header: () => <div className="text-right">Amount</div>,
  //   cell: ({ row }) => {
  //     const amount = Number.parseFloat(row.getValue('amount'))
  //     const formatted = new Intl.NumberFormat('en-US', {
  //       style: 'currency',
  //       currency: 'USD',
  //     }).format(amount)

  //     return <div className="text-right font-medium">{formatted}</div>
  //   },
  // },
  {
    accessorKey: 'current_fight',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Fight#" />
    },
  },
  {
    accessorKey: 'limit_fight',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Limit" />
    },
  },
  {
    accessorKey: 'event_date',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Event date" />
    },
  },
  {
    id: 'actions',
    cell: ({ row }) => {
      const event = row.original

      return <ActionsComponents event={event} />
    },
  },
]
