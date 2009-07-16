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
    public class AuthenticationService : BusinessService
    {
        public const string SERVICE_QUANTUM = "quantum";
        public const string SERVICE_VTC = "vtc";


        public IUser Authenticate(string p_sServiceCode, string p_sUsername, string p_sPassword)
        {
            IUser user = null;

            p_sServiceCode = p_sServiceCode.ToStringSafetyNormalize();
            if (string.IsNullOrEmpty(p_sServiceCode))
            {
                p_sServiceCode = "*";
            }

            bool bFound = false;
            if (!bFound && (p_sServiceCode == "*" || p_sServiceCode == SERVICE_QUANTUM))
            {
                user = AuthenticateQuantum(p_sUsername, p_sPassword);
                bFound = user == null ? false : true;
            }
            if (!bFound && (p_sServiceCode == "*" || p_sServiceCode == SERVICE_VTC))
            {
                user = AuthenticateVTC(p_sUsername, p_sPassword);
                bFound = user == null ? false : true;
            }

            return user;
        }


        /// <summary>
        /// 
        /// </summary>
        /// <param name="p_sUsername"></param>
        /// <param name="p_sPassword"></param>
        /// <returns>null if fail</returns>
        public IUser AuthenticateQuantum(string p_sUsername, string p_sPassword)
        {
            CryptoUtil cu = new CryptoUtil();            

            DbConnection con = Quantum.Tala.Lib.DBHelper.Instance.GetDbConnection("quantum");
            con.Open();
            string strSQL = string.Format("SELECT * FROM User where u='{0}' and password='{1}';", p_sUsername, cu.MD5Hash(p_sPassword));
            DbCommand command = con.CreateCommand();
            command.CommandText = strSQL;
            DbDataReader reader = command.ExecuteReader();

            TalaUser ret = null;
            if (reader.HasRows)
            {
                ret = new TalaUser();
                while (reader.Read())
                {
                    ret.Username = reader["u"] as string;

                    #region Temporary, get the money from the same DB record

                    try
                    {
                        ret.Money = Convert.ToInt32(reader["balance"]);
                    }
                    catch { }

                    #endregion
                }
            }
            con.Close();

            return ret;
            //userDTO userFromQuantumDB = GetUserByUsernameAndHashPassword(p_sUsername, cu.MD5Hash(p_sPassword));
            //if (userFromQuantumDB != null)
            //{
            //    TalaUser u = new TalaUser();
            //    u.Username = p_sUsername;
            //    u.Password = p_sPassword;
            //    return u;
            //}
            //else
            //{
            //    return null;
            //}
        }

        
        public IUser AuthenticateVTC(string p_sUsername, string p_sPassword)
        {
            bool bAuthenticateOK = false;

            CryptoUtil cu = new CryptoUtil();
            
            // TODO: add VTC authentication process here, to verify username password against VTC System
            VTCBillingService.VTCBillingServiceSoapClient ws = new VTCBillingService.VTCBillingServiceSoapClient("VTCBillingServiceSoap12");
            string sVTCReturn = ws.Authenticate(p_sUsername, cu.MD5Hash(p_sPassword));
            VTCResponseInfo response = VTCResponseInfo.Parse(sVTCReturn);

            if (response.ParseOK && int.Parse(response.GetItem(0, "-1")) > 0)
            {
                // login ok
                bAuthenticateOK = true;
            }
            else
            {
                // Login fail
            }

            if (bAuthenticateOK)
            {
                TalaUser userOK = new TalaUser();
                userOK.Username = p_sUsername;
                userOK.Password = p_sPassword;
                return userOK;
            }
            else
            {
                return null;
            }
        }



        [TransactionBound]
        public virtual userDTO GetUserByUsernameAndHashPassword(string username, string hashedPassword)
        {
            userDTO userFromDB = DAU.GetObject<userDTO>(userDTO.U_FLD, username);
            if (userFromDB.password == hashedPassword)
            {
                return userFromDB;
            }
            else
            {
                return null;
            }
        }

    }
}
