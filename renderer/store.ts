import { configureStore, ThunkAction, Action } from '@reduxjs/toolkit'

import siteReducer from './reducers/Sites/sitesSlice'
import downloadsReducer from './reducers/Downloads/downloadsSlice'
import seriesReducer from './reducers/Series/seriesSlice'
import squidReducer from './reducers/Squid/squidSlice'

import { TypedUseSelectorHook, useDispatch, useSelector } from 'react-redux'

export function makeStore() {
  return configureStore({
    reducer: { 
      sites: siteReducer,
      downloads: downloadsReducer,
      series: seriesReducer,
      squid: squidReducer
    },
  })
}

const store = makeStore()

export type AppState = ReturnType<typeof store.getState>

export type AppDispatch = typeof store.dispatch

export type AppThunk<ReturnType = void> = ThunkAction<
  ReturnType,
  AppState,
  unknown,
  Action<string>
>

// Use throughout your app instead of plain `useDispatch` and `useSelector`
export const useAppDispatch = () => useDispatch<AppDispatch>()

export const useAppSelector: TypedUseSelectorHook<AppState> = useSelector


export default store
