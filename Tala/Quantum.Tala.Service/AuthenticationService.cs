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
    public class AuthenticationService : BusinessService, Quantum.Tala.Service.IAuthenticationService
    {
        public const string SERVICE_QUANTUM = "quantum";
        public const string SERVICE_VTC = "vtc";

        [TransactionBound]
        public virtual IUser Authenticate(string p_sServiceCode, string p_sUsername, string p_sPassword)
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
                p_sServiceCode = bFound ? SERVICE_QUANTUM : p_sServiceCode;
            }
            if (!bFound && (p_sServiceCode == "*" || p_sServiceCode == SERVICE_VTC))
            {
                user = AuthenticateVTC(p_sUsername, p_sPassword);
                bFound = user == null ? false : true;
                p_sServiceCode = bFound ? SERVICE_VTC : p_sServiceCode;
            }

            user.System = p_sServiceCode;
            user.FullIdentity = user.Username + "@@" + user.System;

            #region Tạo bản ghi cache user này trong hệ thống tá lả, nếu cần

            if (p_sServiceCode != SERVICE_QUANTUM)
            {
                // TODO: Tạo bản ghi user trong hệ thống tá lả, nếu cần
                userDTO userDBEntry = new userDTO { 
                    
                };
            
            }
            
            #endregion

            return user;
        }


        /// <summary>
        /// 
        /// </summary>
        /// <param name="p_sUsername"></param>
        /// <param name="p_sPassword"></param>
        /// <returns>null if fail</returns>
        [TransactionBound]
        public virtual IUser AuthenticateQuantum(string p_sUsername, string p_sPassword)
        {
            CryptoUtil cu = new CryptoUtil();            
            userDTO userFromDB = DAU.GetObject<userDTO>(userDTO.U_FLD, p_sUsername);
            if (null != userFromDB && userFromDB.password.ToStringSafetyNormalize() == cu.MD5Hash(p_sPassword).ToStringSafetyNormalize())
            {
                TalaUser u = new TalaUser();
                u.Username = p_sUsername;
                u.Password = p_sPassword;
                return u;
            }
            else
            {
                return null;
            }
        }


        [TransactionBound]
        public virtual IUser AuthenticateVTC(string p_sUsername, string p_sPassword)
        {
            CryptoUtil cu = new CryptoUtil();
            return AuthenticateVTC_MD5HashedPassword(p_sUsername, cu.MD5Hash(p_sPassword));            
        }


        [TransactionBound]
        public virtual IUser AuthenticateVTC_MD5HashedPassword(string p_sUsername, string p_sHashedPassword)
        {
            bool bAuthenticateOK = false;

            CryptoUtil cu = new CryptoUtil();

            // VTC authentication process here, to verify username password against VTC System
            VTCBillingService.VTCBillingServiceSoapClient ws = new VTCBillingService.VTCBillingServiceSoapClient("VTCBillingServiceSoap12");
            string sVTCReturn = ws.Authenticate(p_sUsername, p_sHashedPassword);
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
                userOK.Password = p_sHashedPassword;

                // nếu login bằng VTC, thì BankCredential chính là u/p để login với VTC
                userOK.BankCredential = new VTCBankCredential
                {
                    Username = p_sUsername,
                    BankUsername = p_sUsername,
                    BankPassword = p_sHashedPassword
                };

                return userOK;
            }
            else
            {
                return null;
            }
        }


        [TransactionBound]
        public virtual login_logDTO LogLoginAction(string ip, string profilesnapshot, string username)
        {
            login_logDTO logFromDB = new login_logDTO
            {
                ip = ip,
                profile_snapshot = profilesnapshot,
                u = username
            };

            logFromDB = DAU.AddObject<login_logDTO>(logFromDB);
            return logFromDB;
        }
    }
}
