import React, { useEffect } from 'react'
import Layout from '../components/Layout'
import { AppDispatch } from '../store'
import { connect } from'react-redux';
import SquidState from '../model/SquidState';


type Props = {
  configsState: SquidState
	controleMega: (op: 'iniciar' | 'parar') => any
}

const Configs = (props: Props) => {
  
	useEffect(() => {
		if(props.configsState.configMega.status == 'rodando')
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
    
  }
)(Configs)
