using System;
using System.Linq;
using System.Collections;
using System.Collections.Generic;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Xml.Linq;
using GURUCORE.Lib.Core.Security.Cryptography;

using Quantum.Tala.Lib;
using Quantum.Tala.Service.Business;

using Quantum.Tala.Service.Authentication;
using GURUCORE.Framework.Business;
using Quantum.Tala.Service.DTO;
using MySql.Data.Types;



namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// Thread-safe singleton example without using locks
    /// </summary>
    public sealed class Song
    {
        #region Singleton Implementation

        private static readonly Song instance = new Song();

        Song()
        {
            _DicValidAuthkey = new Dictionary<string, string>();
            _DicOnlineUser = new Dictionary<string, TalaUser>();
            _DicSoi = new Dictionary<string, Soi>();
            _DicTournament = new Dictionary<string, tournamentDTO>();
            LoadTournamentFromDB();
            /// THIS FUNCTION RUN BEFORE STATIC CONSTRUCTOR
            // Console.WriteLine("In the internal constructor");            
        }

        // Explicit static constructor to tell C# compiler
        // not to mark type as beforefieldinit
        static Song()
        {
            // Console.WriteLine("In the static constructor");
        }


        /// <summary>
        /// The public Instance property to use
        /// </summary>
        public static Song Instance
        {
            get { return instance; }
        }

        #endregion



        private Dictionary<string, string> _DicValidAuthkey;
        /// <summary>
        /// map (validauthkey - username)
        /// </summary>
        public Dictionary<string, string> DicValidAuthkey
        {
            get { return _DicValidAuthkey; }            
        }

        private Dictionary<string, TalaUser> _DicOnlineUser;
        /// <summary>
        /// map (username - User object)
        /// </summary>
        public Dictionary<string, TalaUser> DicOnlineUser
        {
            get { return _DicOnlineUser; }            
        }    

        private Dictionary<string, Soi> _DicSoi;
        /// <summary>
        /// map (soiID - Soi object)
        /// </summary>
        public Dictionary<string, Soi> DicSoi
        {
            get { return _DicSoi; }            
        }

        private Dictionary<string, tournamentDTO> _DicTournament;
        /// <summary>
        /// map (tournamentid - tournamentDTO object)
        /// </summary>
        public Dictionary<string, tournamentDTO> DicTournament
        {
            get { return _DicTournament; }            
        }





        /// <summary>
        /// Lục tìm trong Dictionary Tournament để tìm với ID đã cho
        /// </summary>
        /// <param name="tournamentid"></param>
        /// <returns>null nếu không tìm thấy</returns>
        public tournamentDTO GetTournamentByID(string tournamentid)
        {
            tournamentDTO tournamentRet;
            DicTournament.TryGetValue(tournamentid, out tournamentRet);
            return tournamentRet;
        }


        /// <summary>
        /// Lục tìm trong Dictionary Soi để tìm với ID đã cho
        /// </summary>
        /// <param name="soiid"></param>
        /// <returns>null nếu không tìm thấy</returns>
        public Soi GetSoiByID(string soiid)
        {
            Soi soiRet;
            DicSoi.TryGetValue(soiid, out soiRet);
            return soiRet;
        }

        /// <summary>
        /// lục tìm trong Dic Soi, chỉ lấy những sới có tourid = tourid đã truyền vào
        /// </summary>
        /// <param name="tournamentid"></param>
        /// <returns></returns>
        public List<Soi> GetSoiByTournamentID(string tournamentid)
        {
            return _DicSoi.Values.Where(soi => soi.DBEntry.tournamentid.ToString() == tournamentid).ToList();
        }


        /// <summary>
        /// Lục tìm trong Dictionary ValidAuthkey
        /// </summary>
        /// <param name="authkey"></param>
        /// <returns>trả về string.empty nếu không tìm thấy</returns>
        public string GetUsernameByAuthkey(string authkey)
        {
            string ret = string.Empty;
            if (DicValidAuthkey.TryGetValue(authkey, out ret))
            {
                return ret;
            }
            else
            {
                return string.Empty;
            }            
        }

        /// <summary>
        /// Lục tìm trong danh sách những user đang Online (Dictionary OnlineUser)
        /// </summary>
        /// <param name="username"></param>
        /// <returns>trả về null nếu không tìm thấy username đang online</returns>
        public TalaUser GetUserByUsername(string username)
        {
            TalaUser ret;
            DicOnlineUser.TryGetValue(username, out ret);
            return ret;
        }

        /// <summary>
        /// Dùng authkey Tìm username, dùng username tìm User
        /// </summary>
        /// <param name="authkey"></param>
        /// <returns></returns>
        public TalaUser GetUserByAuthkey(string authkey)
        {
            return GetUserByUsername(GetUsernameByAuthkey(authkey));
        }





        /// <summary>
        /// Kiểm tra authentication để cho phép user có được login vào hệ thống hay không.
        /// </summary>
        /// <param name="username">username của user cần login</param>
        /// <param name="password">password của user cần login</param>
        /// <returns></returns>
        public TalaUser LoginVaoSongChoi(string username, string password)
        {
            if (username.IsNullOrEmpty() || password.IsNullOrEmpty())
            {
                return null;
            }

            IAuthenticationService authensvc = ServiceLocator.Locate<IAuthenticationService, AuthenticationService>();
            TalaUser user = authensvc.Authenticate("*", username, password) as TalaUser;

            /// nếu tìm thấy user ở một kho lưu nào đó (DB của Quantum hoặc AuthenStore của đơn vị khác)
            if (user != null && user.Username == username)
            {
                // if found user with username and password, authenticate OK
                if (Song.Instance.DicOnlineUser.ContainsKey(user.Username) == false)
                {
                    // lần đầu, tạo authkey mới, thêm vào các mảng cache
                    // generate new authkey
                    user.Authkey = user.Username + "^" + FunctionExtension.GetRandomGUID();

                    // TODO: chỗ này chỉ dùng test, bỏ dòng dưới đi
                    #if DEBUG
                    user.Authkey = user.Username;
                    #endif

                    Song.Instance.DicOnlineUser.Add(user.Username, user);
                    Song.Instance.DicValidAuthkey.Add(user.Authkey, user.Username);
                }
                else
                {
                    // lần login lại, lấy thông tin authenticated cũ ra, trả lại
                    user = Song.Instance.DicOnlineUser[user.Username];
                }
                
                // user.UserStatDBEntry
            }
            else
            {
                // login fail
                user = null;
            }

            return user;
        }
                

        /// <summary>
        /// Tạo sới FREE mới, ấn creator vào sới nếu ok. Nếu creator đã có sới rồi, trả lại sới cũ
        /// </summary>
        /// <param name="sName">tên sới, để gợi nhớ</param>
        /// <param name="ownerUsername">ai tạo sới này</param>
        /// <returns></returns>
        public Soi CreatNewFreeSoi(string sName, string ownerUsername)
        {
            Soi soiRet = null;

            TalaUser user = GetUserByUsername(ownerUsername);
            if (user != null)
            {
                if (user.CurrentSoi == null)
                {
                    ITournamentService toursvc = ServiceLocator.Locate<ITournamentService, TournamentService>();
                    soiDTO soiDBEntry = new soiDTO { 
                        desc = "",
                        dt = new MySqlDateTime(DateTime.Now),
                        name = sName,
                        owner = ownerUsername,
                        tournamentid = 1 // NOTE: cố định tour free = 1
                    };
                    
                    soiDBEntry.id = toursvc.CreateSoi(soiDBEntry);
                    // create new
                    soiRet = new Soi(soiDBEntry.id, sName, ownerUsername);
                    soiRet.DBEntry = soiDBEntry;

                    Song.Instance.DicSoi.Add(soiRet.ID.ToString(), soiRet);

                    // nhồi luôn người tạo vào sới
                    soiRet.AddPlayer(ownerUsername);
                }
                else
                {
                    // return current Soi
                    soiRet = user.CurrentSoi;
                }
            }           
            
            return soiRet;
        }


        /// <summary>
        /// Hàm này chỉ dùng để cho quản trị tạo sới, không được public qua enduser API gọi
        /// </summary>
        /// <param name="sName"></param>
        /// <param name="tournamentid"></param>
        /// <returns></returns>
        public Soi CreatNewSoiOfTour(string p_sName, int p_nTournamentid)
        {
            Soi soiRet = null;        
            
            ITournamentService toursvc = ServiceLocator.Locate<ITournamentService, TournamentService>();
            soiDTO soiDBEntry = new soiDTO
            {
                desc = "",
                dt = new MySqlDateTime(DateTime.Now),
                name = p_sName,                
                tournamentid = p_nTournamentid
            };

            // tạo mới trong DB
            soiDBEntry.id = toursvc.CreateSoi(soiDBEntry);
            // tạo mới trong bộ nhớ, đính đối tượng trong DB vào
            soiRet = new Soi(soiDBEntry.id, p_sName, string.Empty);
            soiRet.DBEntry = soiDBEntry;

            // đẩy vào danh mục trong bộ nhớ
            Song.Instance.DicSoi.Add(soiRet.ID.ToString(), soiRet);

            return soiRet;
        }


        /// <summary>
        /// Xoá một sới theo ID cho trước
        /// </summary>
        /// <param name="sSoiID"></param>
        /// <returns></returns>
        public bool DeleteSoi(string sSoiID)

        {
            Soi soiToDelete = GetSoiByID(sSoiID);
            if (soiToDelete == null)
            {
                return false;
            }
            else
            {
                // đuổi hết khách chơi
                foreach (Seat seat in soiToDelete.SeatList)
                {
                    seat.Player.CurrentSoi = null;
                }

                // huỷ ván
                soiToDelete.CurrentVan = null;
                return Song.Instance.DicSoi.Remove(sSoiID);
            }                        

        }


        /// <summary>
        /// lấy các bản ghi trong DB về Tournament, ghi vào bộ nhớ
        /// </summary>
        /// <returns></returns>
        public bool LoadTournamentFromDB()
        {
            ITournamentService toursvc = ServiceLocator.Locate<ITournamentService, TournamentService>();
            tournamentDTO[] arr = toursvc.GetTournamentList();

            _DicTournament.Clear();
            foreach (tournamentDTO tour in arr)
            {
                _DicTournament.Add(tour.id.ToString(), tour);
            }
            return true;
        }

        public bool ProcessSoiValidity()
        {
            bool bRet = false;

            // TODO: xử lý các trường hợp đúng sai trong logic game, mỗi khi có một lần gọi đến hàm này
            /// 

            return bRet;
        }
    }
}
