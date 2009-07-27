using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using GURUCORE.Framework.Business;
using Quantum.Tala.Service.DTO;
using GURUCORE.Framework.DataAccess;
using Quantum.Tala.Service.Business;

namespace Quantum.Tala.Service
{
    public class PlayingService : BusinessService, Quantum.Tala.Service.IPlayingService
    {
        public const int STARTUP_POINT_USER_STAT = 500;

        [TransactionBound]
        public virtual void AdjustGold(string p_sUsername, int p_nValue, EnumPlayingResult p_enumWhy, int p_nTourID)
        {
            string sSQL = "update {0} set {1} point=point+({2}) where u='{3}'";            


            string sWinLose = string.Empty;
            if(p_enumWhy == EnumPlayingResult.Win)
            {
                sWinLose = "win=win+1,";
            }
            else if(p_enumWhy == EnumPlayingResult.Lose)
            {
                sWinLose = "lose=lose+1,";
            }
            string sTablename = "playing_user_stat";            
            
            object oRet = DAU._ExecuteNonQuery(string.Format(sSQL, sTablename, sWinLose, p_nValue, p_sUsername));
            
            if ((int)oRet <= 0)
            {
                user_statDTO dto = new user_statDTO();
                dto.u = p_sUsername;
                dto.win = (p_enumWhy == EnumPlayingResult.Win) ? 1 : 0;
                dto.lose = (p_enumWhy == EnumPlayingResult.Lose) ? 1 : 0;
                dto.point = STARTUP_POINT_USER_STAT + p_nValue;
                dto = DAU.AddObject<user_statDTO>(dto);                
            }

            sTablename = "game_user_tournament";
            sSQL += " and tournamentid=" + p_nTourID;
            oRet = DAU._ExecuteNonQuery(string.Format(sSQL, sTablename, sWinLose, p_nValue, p_sUsername));
        }        
        
    }
}
