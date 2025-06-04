// import { Button } from '@/components/ui/button'
import { Checkbox } from '@/components/ui/checkbox'
// import {
//   DropdownMenu,
//   DropdownMenuContent,
//   DropdownMenuItem,
//   DropdownMenuLabel,
//   DropdownMenuSeparator,
//   DropdownMenuTrigger,
// } from '@/components/ui/dropdown-menu'
import { formatMoney } from '@/lib/utils'
// import { router } from '@inertiajs/react'
// import { MoreHorizontal } from 'lucide-react'
import { DataTableColumnHeader } from './data-table-column-header'

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
    enableHiding: true,
  },
  {
    accessorKey: 'id',
    header: ({ column }) => {
      return <DataTableColumnHeader className="hidden" column={column} title="#" />
    },
    cell: ({ row }) => <div className="hidden">{row.getValue('id')}</div>,
  },

  {
    accessorKey: 'fight_no',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Fight No." />
    },
  },

  {
    accessorKey: 'amount',
    header: () => <div className="text-right">Amount</div>,
    cell: ({ row }) => {
      const amount = Number.parseFloat(row.getValue('amount'))
      return <div className="text-right font-medium">{formatMoney(amount)}</div>
    },
  },

  {
    accessorKey: 'side',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Side" />
    },
  },
  {
    accessorKey: 'status',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Status" />
    },
  },

  {
    accessorKey: 'created_at',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Date" />
    },
  },
  // {
  //   id: 'actions',
  //   cell: ({ row }) => {
  //     const event = row.original

  //     return (
  //       <DropdownMenu>
  //         <DropdownMenuTrigger asChild>
  //           <Button variant="ghost" className="h-8 w-8 p-0">
  //             <span className="sr-only">Open menu</span>
  //             <MoreHorizontal className="h-4 w-4" />
  //           </Button>
  //         </DropdownMenuTrigger>
  //         <DropdownMenuContent align="end">
  //           <DropdownMenuLabel>Actions</DropdownMenuLabel>
  //           <DropdownMenuItem
  //             onClick={() => navigator.clipboard.writeText(event.id)}
  //           >
  //             Copy event ID
  //           </DropdownMenuItem>
  //           <DropdownMenuSeparator />
  //           <DropdownMenuItem onClick={() => router.post(`/event/activate/${event.id}`)} disabled={event.status === 'ACTIVE'}>Activate</DropdownMenuItem>
  //           <DropdownMenuItem>Edit</DropdownMenuItem>
  //           <DropdownMenuItem>Delete</DropdownMenuItem>
  //         </DropdownMenuContent>
  //       </DropdownMenu>
  //     )
  //   },
  // },
]
