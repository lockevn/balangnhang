using System;
using GURUCORE.Framework.Business;
using GURUCORE.Framework.DataAccess;
using GURUCORE.Framework.DataAccess.ORMapping;
using Quantum.Tala.Lib;
using Quantum.Tala.Service.Authentication;
using Quantum.Tala.Service.Business;
using Quantum.Tala.Service.DTO;
using GURUCORE.Lib.Core.Security.Cryptography;
using System.Data.Common;


namespace Quantum.Tala.Service
{
    public class UserProfileService : BusinessService, Quantum.Tala.Service.IUserProfileService
    {
        [TransactionBound]
        public virtual user_statDTO GetUserPlayStat(string p_sUsername)
        {
            user_statDTO ret = DAU.GetObject<user_statDTO>(user_statDTO.U_FLD, p_sUsername);
            return ret;
        }        
    }
}
