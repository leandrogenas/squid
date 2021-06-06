import Configs from "../../model/Configs";
import { lsDB } from "../../model/SquidLSDB";

export async function salvarConfigs(confs: Configs)
{
    return lsDB.setItem('configs', JSON.stringify(confs));
}