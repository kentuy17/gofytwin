/* eslint-disable no-console */
import { Button } from '@/components/ui/button'
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
  DropdownMenuCheckboxItem,
  DropdownMenuContent,
  // DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

import { Input } from '@/components/ui/input'

import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

import { cn } from '@/lib/utils'
import { useForm } from '@inertiajs/react'

import {
  flexRender,
  getCoreRowModel,
  getFilteredRowModel,
  getPaginationRowModel,
  getSortedRowModel,
  useReactTable,
} from '@tanstack/react-table'
import { format } from 'date-fns'
import { formatInTimeZone } from 'date-fns-tz'
import { CalendarIcon, PlusCircle, Settings2 } from 'lucide-react'
import { useEffect, useRef, useState } from 'react'
import { toast } from 'sonner'
import { Calendar } from '../ui/calendar'
import { Label } from '../ui/label'
import { Popover, PopoverContent, PopoverTrigger } from '../ui/popover'
import { DataTablePagination } from './data-table-pagination'

function AddEvent({ children }) {
  const [open, setOpen] = useState(false)
  const [date, setDate] = useState(new Date())
  const submitRef = useRef(null)

  const { data, setData, post } = useForm({
    event_name: null,
    date: null,
  })

  const handleSumbit = (e) => {
    e.preventDefault()
    post('/event/add', {
      onSuccess: () => {
        setOpen(false)
        toast('Success!', {
          description: 'Event has been added.',
          action: {
            label: 'OK',
            onClick: () => console.log('OK'),
          },
        })
      },
      onError: (errors) => {
        toast('Error!', {
          description: JSON.stringify(errors),
          action: {
            label: 'OK',
            onClick: () => console.log(errors),
          },
        })
      },
    })
  }

  useEffect(() => {
    // const d = new Date(date)
    // console.log(date)
    const localizedDate = formatInTimeZone(date, 'Asia/Manila', 'yyyy-MM-dd HH:mm:ss zzz')
    setData('date', localizedDate)
    console.log(localizedDate)
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [date])

  return (
    <Dialog open={open} onOpenChange={setOpen}>
      <DialogTrigger asChild>
        {children}
      </DialogTrigger>
      <DialogContent className="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Input Details</DialogTitle>
        </DialogHeader>
        <div className="grid gap-4 py-4">
          <div className="grid grid-cols-1 items-center gap-4">
            <div className="grid gap-3">
              <Label htmlFor="name-1">Event Name</Label>
              <Input
                id="name-1"
                defaultValue={data.event_name}
                onChange={e => setData('event_name', e.target.value)}
                name="name"
              />
            </div>
            <div className="grid gap-3">
              <Label htmlFor="username-1">Date</Label>
              {/* <Input id="username-1" name="username" defaultValue="@peduarte" /> */}
              <div>
                <DatePickerComponent asChild changeDate={setDate}>
                  <Button
                    variant="outline"
                    className={cn(
                      'w-full justify-start text-left font-normal',
                      !date && 'text-muted-foreground',
                    )}
                  >
                    <CalendarIcon />
                    {date ? format(date, 'P') : <span>Pick a date</span>}
                  </Button>
                </DatePickerComponent>
              </div>

            </div>
          </div>
        </div>
        <DialogFooter>
          <Button type="submit" ref={submitRef} onClick={handleSumbit}>
            Add Event
          </Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>
  )
}

export function DatePickerComponent({ children, changeDate }) {
  const [open, setOpen] = useState(false)
  const [date, setDate] = useState()
  const handleDateSelected = (e) => {
    setDate(e)
    changeDate(e)
    setOpen(!open)
  }

  return (
    <Popover open={open} onOpenChange={setOpen} modal>
      <PopoverTrigger asChild>
        {children}
      </PopoverTrigger>
      <PopoverContent className="w-auto p-0" align="start">
        <Calendar
          mode="single"
          selected={date}
          onSelect={handleDateSelected}
          initialFocus
        />
      </PopoverContent>
    </Popover>
  )
}

export function DataTable({
  columns,
  data,
}) {
  const [sorting, setSorting] = useState([])
  const [columnFilters, setColumnFilters] = useState([])
  const [columnVisibility, setColumnVisibility] = useState({})
  const [rowSelection, setRowSelection] = useState({})

  const table = useReactTable({
    data,
    columns,
    getCoreRowModel: getCoreRowModel(),
    getPaginationRowModel: getPaginationRowModel(),
    onSortingChange: setSorting,
    getSortedRowModel: getSortedRowModel(),
    onColumnFiltersChange: setColumnFilters,
    getFilteredRowModel: getFilteredRowModel(),
    onColumnVisibilityChange: setColumnVisibility,
    onRowSelectionChange: setRowSelection,
    state: {
      sorting,
      columnFilters,
      columnVisibility,
      rowSelection,
    },
  })

  // const handleAddEvent = () => {
  //   router.post('/event/add', {})
  //   router.on('finish', (e) => {
  //     console.log(e)
  //   })
  // }

  return (
    <div>
      <div className="flex items-center justify-between py-4">
        <Input
          placeholder="Filter name..."
          value={(table.getColumn('name')?.getFilterValue()) ?? ''}
          onChange={event =>
            table.getColumn('name')?.setFilterValue(event.target.value)}
          className="max-w-sm"
        />
        <div className="flex gap-3">
          <DropdownMenu>
            <DropdownMenuTrigger asChild>
              <Button variant="outline" className="ml-auto">
                <Settings2 />
                View
              </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
              <DropdownMenuLabel>Toggle columns</DropdownMenuLabel>
              <DropdownMenuSeparator />
              {table
                .getAllColumns()
                .filter(
                  column => column.getCanHide(),
                )
                .map((column) => {
                  return (
                    <DropdownMenuCheckboxItem
                      key={column.id}
                      className="capitalize"
                      checked={column.getIsVisible()}
                      onCheckedChange={value =>
                        column.toggleVisibility(!!value)}
                    >
                      {column.id}
                    </DropdownMenuCheckboxItem>
                  )
                })}
            </DropdownMenuContent>
          </DropdownMenu>
          <AddEvent>
            <Button variant="outline" className="ml-auto">
              <PlusCircle />
              Add
            </Button>
          </AddEvent>
          {/* <DatePickerComponent /> */}
        </div>

      </div>
      <div className="grid grid-cols-1 rounded-md border">
        <Table>
          <TableHeader className="">
            {table.getHeaderGroups().map(headerGroup => (
              <TableRow key={headerGroup.id}>
                {headerGroup.headers.map((header) => {
                  return (
                    <TableHead key={header.id}>
                      {header.isPlaceholder
                        ? null
                        : flexRender(
                            header.column.columnDef.header,
                            header.getContext(),
                          )}
                    </TableHead>
                  )
                })}
              </TableRow>
            ))}
          </TableHeader>
          <TableBody>
            {table.getRowModel().rows?.length
              ? (
                  table.getRowModel().rows.map(row => (
                    <TableRow
                      key={row.id}
                      // data-state={row.getIsSelected() && 'selected'}
                      data-state={(row.original.status === 'ACTIVE') && 'selected'}
                    >
                      {row.getVisibleCells().map(cell => (
                        <TableCell key={cell.id}>
                          {flexRender(cell.column.columnDef.cell, cell.getContext())}
                        </TableCell>
                      ))}
                    </TableRow>
                  ))
                )
              : (
                  <TableRow>
                    <TableCell colSpan={columns.length} className="h-24 text-center">
                      No results.
                    </TableCell>
                  </TableRow>
                )}
          </TableBody>
        </Table>
      </div>
      <DataTablePagination table={table} />

    </div>

  )
}
