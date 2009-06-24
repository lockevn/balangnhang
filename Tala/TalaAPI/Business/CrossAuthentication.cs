using System;
using System.Collections.Generic;
using System.Linq;

using TalaAPI.Lib;
using GURUCORE.Lib.Core.Security.Cryptography;


namespace TalaAPI.Business
{
    /// <summary>
    /// Nghiệp vụ kiểm tra định danh tài khoản của người dùng
    /// </summary>
    public class CrossAuthentication
    {
        public const string SERVICE_QUANTUM = "quantum";
        public const string SERVICE_VTC = "vtc";


        public static User Authenticate(string p_sServiceCode, string p_sUsername, string p_sPassword)
        {
            User user = null;

            p_sServiceCode = p_sServiceCode.ToStringSafetyNormalize();
            if (string.IsNullOrEmpty(p_sServiceCode))
            {
                p_sServiceCode = "*";
            }

            bool bFound = false;
            if (!bFound && (p_sServiceCode == "*" || p_sServiceCode == SERVICE_QUANTUM))
            {
                user = CrossAuthentication.AuthenticateQuantum(p_sUsername, p_sPassword);
                bFound = user == null ? false : true;
            }
            if (!bFound && (p_sServiceCode == "*" || p_sServiceCode == SERVICE_VTC))
            {
                user = new User("vtcuser", "vtcauthkey");
                bFound = user == null ? false : true;
            }

            return user;
        }


        public static User AuthenticateQuantum(string p_sUsername, string p_sPassword)
        {
            CryptoUtil cu = new CryptoUtil();
            p_sPassword = cu.MD5Hash(p_sPassword);
            User user = DBUtil.GetUserByUsernameAndPassword(p_sUsername, p_sPassword);
            return user;
        }

    }
}
