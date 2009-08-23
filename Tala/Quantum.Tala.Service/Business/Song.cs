using System;
using System.Linq;
using System.Collections;
using System.Collections.Generic;
using System.Data;
using System.Configuration;
using System.Xml.Linq;
using GURUCORE.Lib.Core.Security.Cryptography;

using Quantum.Tala.Lib;
using Quantum.Tala.Service.Business;

using Quantum.Tala.Service.Authentication;
using GURUCORE.Framework.Business;
using Quantum.Tala.Service.DTO;
using MySql.Data.Types;
using System.Web;
using System.Web.Caching;



namespace Quantum.Tala.Service.Business
{
    /// <summary>
    /// Thread-safe singleton example without using locks
    /// </summary>
    public sealed class Song
    {
        /// <summary>
        /// 2700 giây, 45 phút, thời gian cho phép user tồn tại trên hệ thống, kể từ khi login hoặc last action
        /// </summary>
        private const double AUTHENTICATE_TIMEOUT = 2700d;

        public const string CACHE_PREFIX_AUTHKEY = "authkey_";
        public const string CACHE_PREFIX_USERNAME = "username_";
        


        // this is single IL command, so it is atomic.
        private static object m_oLockInstance = new object();


        /// <summary>
        /// map (validauthkey - User object)
        /// </summary>        
        private Cache _DicValidAuthkey;        

        private Cache _DicOnlineUser;
        /// <summary>
        /// a snapshot of map (username - User object)
        /// </summary>
        public Dictionary<string, TalaUser> DicOnlineUser
        {            
            get 
            {
                return _DicOnlineUser.OfType<DictionaryEntry>()
                    .Where(e => e.Key.ToString().StartsWith(Song.CACHE_PREFIX_AUTHKEY) )
                    .ToDictionary(e => e.Key.ToString(), e => e.Value as TalaUser);
            }
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

        private Dictionary<int, List<string>> _DicTournamentWaitingList;
        /// <summary>
        /// map tournamentid - list(username waiting in this tournament)
        /// </summary>
        public Dictionary<int, List<string>> DicTournamentWaitingList
        {
            get { return _DicTournamentWaitingList; }
        }
            


        #region Singleton Implementation


        private static Song instance;

        private Song()
        {
            _DicValidAuthkey = HttpContext.Current.Cache;
            _DicOnlineUser = HttpContext.Current.Cache;
            _DicSoi = new Dictionary<string, Soi>();
            _DicTournament = new Dictionary<string, tournamentDTO>();
            _DicTournamentWaitingList = new Dictionary<int, List<string>>();
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
            get 
            {
                if (null == instance)
                {
                    lock (m_oLockInstance)
                    {
                        instance = new Song();
                    }
                }

                return instance;
            }
        }

        #endregion



        #region In this section, all function are needed to lock/sync while using share resource (Dic in this class)
        
        
        /// <summary>
        /// Lục tìm trong Dictionary Tournament để tìm với ID đã cho
        /// </summary>
        /// <param name="tournamentid"></param>
        /// <returns>null nếu không tìm thấy</returns>
        public tournamentDTO GetTournamentByID(string tournamentid)
        {
            tournamentDTO tournamentRet;
            _DicTournament.TryGetValue(tournamentid, out tournamentRet);
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
            _DicSoi.TryGetValue(soiid, out soiRet);
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
        /// Lục tìm trong danh sách những user đang Online (Dictionary OnlineUser)
        /// </summary>
        /// <param name="username"></param>
        /// <returns>trả về null nếu không tìm thấy username đang online</returns>
        public TalaUser GetUserByUsername(string username)
        {
            TalaUser ret = _DicOnlineUser.Get(Song.CACHE_PREFIX_USERNAME + username) as TalaUser;
            return ret;
        }

        /// <summary>
        /// Dùng authkey Tìm username, dùng username tìm User
        /// </summary>
        /// <param name="authkey"></param>
        /// <returns></returns>
        public TalaUser GetUserByAuthkey(string authkey)
        {
            return _DicValidAuthkey.Get(Song.CACHE_PREFIX_AUTHKEY + authkey) as TalaUser;            
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
                lock (m_oLockInstance)
                {
                    // if found user with username and password, authenticate OK
                    if (_DicOnlineUser.Get(Song.CACHE_PREFIX_USERNAME + user.Username) == null)
                    {
                        // lần đầu, tạo authkey mới, thêm vào các mảng cache
                        // generate new authkey
                        user.Authkey = user.Username + "^" + FunctionExtension.GetRandomGUID();

                        // chỗ này chỉ dùng test, khi release bỏ dòng dưới đi
#if DEBUG
                        user.Authkey = user.Username;
#endif

                        CreateVolatineAuthenticateInfo(user.Authkey, user);                        
                    }
                    else
                    {
                        // lần login lại, lấy thông tin authenticated cũ ra, trả lại
                        user = _DicOnlineUser[Song.CACHE_PREFIX_USERNAME + user.Username] as TalaUser;
                    }
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
        /// tạo thông tin trong cache (bay hơi từ lần tạo cuối cùng) để lưu trữ user, authkey đang login và valid trong hệ thống
        /// </summary>
        /// <param name="authkey"></param>
        /// <param name="user"></param>
        public void CreateVolatineAuthenticateInfo(string authkey, TalaUser user)
        {
            // create cache dạng timeout tính từ last recent use
            _DicOnlineUser.Insert(
                Song.CACHE_PREFIX_USERNAME + user.Username, user,
                null,
                DateTime.MaxValue, TimeSpan.FromSeconds(Song.AUTHENTICATE_TIMEOUT)
                );

            _DicValidAuthkey.Insert(
                Song.CACHE_PREFIX_AUTHKEY + authkey, user,
                null,
                DateTime.MaxValue, TimeSpan.FromSeconds(Song.AUTHENTICATE_TIMEOUT)
                );
            throw new NotImplementedException();
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
                        tournamentid = (int)TournamentType.Free
                    };
                    
                    soiDBEntry.id = toursvc.CreateSoi(soiDBEntry);
                    // create new
                    soiRet = new Soi(soiDBEntry.id, sName, ownerUsername);
                    soiRet.DBEntry = soiDBEntry;
                    // nhồi luôn người tạo vào sới
                    soiRet.AddPlayer(user);

                    lock (m_oLockInstance)
                    {
                        _DicSoi.Add(soiRet.ID.ToString(), soiRet);
                    }
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

            lock (m_oLockInstance)
            {
                // đẩy vào danh mục trong bộ nhớ
                _DicSoi.Add(soiRet.ID.ToString(), soiRet);
            }

            return soiRet;
        }


        /// <summary>
        /// Xoá một sới theo ID cho trước
        /// </summary>
        /// <param name="sSoiID"></param>
        /// <returns></returns>
        public bool DeleteSoi(string sSoiID)
        {
            bool bRet = false;

            Soi soiToDelete = GetSoiByID(sSoiID);
            if (soiToDelete != null)
            {
                lock (m_oLockInstance)
                {
                    // đuổi hết khách chơi
                    foreach (Seat seat in soiToDelete.SeatList)
                    {
                        seat.Player.CurrentSoi = null;
                    }

                    // huỷ ván
                    soiToDelete.CurrentVan = null;
                    bRet = _DicSoi.Remove(sSoiID);
                }
            }
            return bRet;
        }


        /// <summary>
        /// lấy các bản ghi trong DB về Tournament, ghi vào bộ nhớ
        /// </summary>
        /// <returns></returns>
        public bool LoadTournamentFromDB()
        {
            ITournamentService toursvc = ServiceLocator.Locate<ITournamentService, TournamentService>();
            tournamentDTO[] arr = toursvc.GetTournamentList();

            lock (m_oLockInstance)
            {
                _DicTournament.Clear();
                _DicTournamentWaitingList.Clear();
                foreach (tournamentDTO tour in arr)
                {
                    _DicTournament.Add(tour.id.ToString(), tour);
                    _DicTournamentWaitingList.Add(tour.id, new List<string>());
                }
            }

            return true;
        }



        public void Logout(string sAuthkey, string sUsername)
        {
            _DicValidAuthkey.Remove(Song.CACHE_PREFIX_AUTHKEY + sAuthkey);
            _DicOnlineUser.Remove(Song.CACHE_PREFIX_USERNAME + sUsername);
        
            lock(m_oLockInstance)
            {
                try
                {
                    foreach (var waitingListOfTour in _DicTournamentWaitingList.Values)
                    {
                        waitingListOfTour.Remove(sUsername);
                    }
                }
                catch { }
            }
        }

        #endregion

    }
}
