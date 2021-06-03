import React, { useEffect } from 'react'
import Layout from '../components/Layout'
import { AppDispatch } from '../store'
import { ListagemConfigs } from '../types';
import { connect } from 'react-redux';
import { iniciarMegaThunk, pararMegaThunk } from '../reducers/Squid';


type Props = {
  configsState: ListagemConfigs
	controleMega: (op: 'iniciar' | 'parar') => any
}

const Configs = (props: Props) => {
  
	useEffect(() => {
		if(props.configsState.configMega.status == 'iniciando')
			return

		//props.controleMega('iniciar');

	})

	return (
		<Layout title={`Downloads`}>
			<style jsx global>{`
				
			`}</style>

		</Layout>
	)
}

function mapStateToProps(state) {
  return {
    configsState: state.configs
  }
}

export default connect(
  mapStateToProps,
  {
    controleMega: (op: 'iniciar' | 'parar') => {
      return function (dispatch: AppDispatch) {
        return (op == 'iniciar')
            ? dispatch(iniciarMegaThunk())
            : dispatch(pararMegaThunk())
      }
    }
  }
)(Configs)
