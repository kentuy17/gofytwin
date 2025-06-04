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
    accessorKey: 'merchant_order_no',
    header: ({ column }) => {
      return <DataTableColumnHeader column={column} title="Order No." />
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
]
