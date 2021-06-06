
class SquidLSDB extends Storage {


    private constructor()
    {
        super();
    }

    static getInstance()
    {
        if(!window || !window.localStorage)
            throw new Error('LocalStorage não está disponível');

        return new SquidLSDB;
    }

}

export const lsDB = SquidLSDB.getInstance();

export default SquidLSDB;