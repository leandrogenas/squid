export default interface ConfigMega {
	status: 'desconhecido' | 'rodando' | 'parado' | 'erro',
	pidServer?: number
	pidShell?: number
	stdout: string[]
}