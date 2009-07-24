using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using GURUCORE.Framework.Business;
using Quantum.Tala.Service.DTO;
using GURUCORE.Framework.DataAccess;

namespace Quantum.Tala.Service
{
    public class PlayingService : BusinessService, Quantum.Tala.Service.IPlayingService
    {
        [TransactionBound]
        public virtual int CreateUserStat(string username)
        {
            user_statDTO dto = new user_statDTO
            {
                win = 0,
                lose = 0,
                point = 500,
                u = username
            };

            return DAU.AddObject<user_statDTO>(dto).id;
        }
    }
}
