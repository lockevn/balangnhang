using System;
using System.Collections;
using System.Collections.Generic;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Xml.Linq;
using GURUCORE.Lib.Core.Security.Cryptography;

using TalaAPI.Lib;
using TalaAPI.Business;
using TalaAPI.XMLRenderOutput;


namespace TalaAPI.Business
{
    /// <summary>
    /// Thread-safe singleton example without using locks
    /// </summary>
    public sealed class Song
    {
        private static readonly Song instance = new Song();

        // Explicit static constructor to tell C# compiler
        // not to mark type as beforefieldinit
        static Song()
        {
        }

        Song()
        {
        }

        /// <summary>
        /// The public Instance property to use
        /// </summary>
        public static Song Instance
        {
            get { return instance; }
        }

        public string Text;
        
        public Dictionary<string, string> ValidAuthkey = new Dictionary<string, string>();
        public Dictionary<string, User> OnlineUser = new Dictionary<string,User>();
        public Dictionary<string, Soi> Soi = new Dictionary<string,Soi>();

        public Soi GetSoiByID(string soiid)
        {            
            return Soi[soiid];            
        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="authkey"></param>
        /// <returns>trả về string.empty nếu không tìm thấy</returns>
        public string GetUsernameByAuthkey(string authkey)
        {
            string ret = string.Empty;
            if (ValidAuthkey.TryGetValue(authkey, out ret))
            {
                return ret;
            }
            else
            {
                return string.Empty;
            }            
        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="username"></param>
        /// <returns>trả về null nếu không tìm thấy username đang online</returns>
        public User GetUserByUsername(string username)
        {
            User ret;
            OnlineUser.TryGetValue(username, out ret);
            return ret;
        }

        public User GetUserByAuthkey(string authkey)
        {
            return GetUserByUsername(GetUsernameByAuthkey(authkey));
        }

        public User LoginVaoSongChoi(string username, string password)
        {
            if (username.IsNullOrEmpty() || password.IsNullOrEmpty())
            {
                return null;
            }
            else
            {
                CryptoUtil cu = new CryptoUtil();
                password = cu.MD5Hash(password);
                User user = DBUtil.GetUserByUsernameAndPassword(username, password);
                if (user != null && user.Username == username)
                {
                    // if found user with username and password
                    // generate new authkey
                    
                    if (Song.Instance.OnlineUser.ContainsKey(user.Username) == false)
                    {
                        // lần đầu, tạo authkey mới, thêm vào các mảng cache
                        user.Authkey = TextUtil.GetRandomGUID();

                        // TODO: bỏ dòng dưới đi
                        user.Authkey = user.Username;

                        Song.Instance.OnlineUser.Add(user.Username, user);
                        Song.Instance.ValidAuthkey.Add(user.Authkey, user.Username);
                    }
                    else
                    {
                        // lần login lại, với username và password nhập đúng, lấy user cũ ra, trả cũ authkey về
                        user = Song.Instance.OnlineUser[user.Username];
                    }
                }


                return user;
            }

        }

        /// <summary>
        /// 
        /// </summary>
        /// <param name="sName"></param>
        /// <param name="ownerUsername"></param>
        /// <returns></returns>
        public Soi CreatSoiMoi(string sName, string ownerUsername)
        {
            Soi soi = new Soi(Song.Instance.Soi.Count + 1, sName, ownerUsername);
            Song.Instance.Soi.Add(soi.Id.ToString(), soi);
            soi.AddPlayer(ownerUsername);
            return soi;
        }
    }
}
