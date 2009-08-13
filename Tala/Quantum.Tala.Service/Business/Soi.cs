using System;
using System.Data;
using System.Configuration;
using System.Linq;
using System.Xml.Linq;
using System.Collections.Generic;

using Quantum.Tala.Lib;

using Quantum.Tala.Lib.XMLOutput;
using Quantum.Tala.Service.Authentication;
using System.Text;
using System.Web;
using Quantum.Tala.Service.DTO;
using GURUCORE.Framework.Business;
using MySql.Data.Types;


namespace Quantum.Tala.Service.Business
{
    public class Soi : APIDataEntry
    {
        public soiDTO DBEntry { get; set; }

        int _ID;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public int ID
        {
            get { return _ID; }
            set { _ID = value; }
        }

        string _Name;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public string Name
        {
            get { return _Name; }
            set { _Name = value; }
        }
        
        string _OwnerUsername;
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public string OwnerUsername
        {
            get
            {
                return _OwnerUsername;
            }
            set { _OwnerUsername = value; }
        }

        public DateTime StartTime { get; set; }
        public DateTime EndTime { get; set; }
        public string Description { get; set; }
       
                
        /// <summary>
        /// số CHIP đang ở trong Gà
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public int GaValue { get; set; }

        
        /// <summary>
        /// Nếu trường này bằng true thì sới này không thay đổi được luật hay option nữa.
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public bool IsLocked
        {
            get;
            set;
        }
        
        
        /// <summary>
        /// Nếu trường này bằng true, thì sới này đang có ván đang chơi, ván đang diễn ra. Nếu trường này bằng false, ván chưa bắt đầu hoặc đã kết thúc. Lúc này client cần đọc thông tin về kết quả ván chơi trước , ...
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public bool IsPlaying
        { get; set; }





        List<Seat> _SeatList;
        /// <summary>
        /// Danh sách các chỗ ngồi chơi trong sới
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag, DataListizeType.ListGeneric, false, false)]
        public List<Seat> SeatList
        {
            get { return _SeatList; }
            set { _SeatList = value; }
        }

        Option _SoiOption;
        /// <summary>
        /// Các tuỳ chọn của Sới này, setup khi bắt đầu sới mới
        /// </summary>
        [ElementXMLExportAttribute("", DataOutputXMLType.NestedTag)]
        public Option SoiOption
        {
            get { return _SoiOption; }
            set { _SoiOption = value; }
        }

        Van _CurrentVan;
        /// <summary>
        /// Ván đang chơi của Sới hiện tại
        /// </summary>
        public Van CurrentVan
        {
            get { return _CurrentVan; }
            set { _CurrentVan = value; }
        }





        /// <summary>
        /// Khởi tạo đối tượng sới, cho các Player gia nhập
        /// </summary>
        /// <param name="id"></param>
        /// <param name="name"></param>
        /// <param name="owner"></param>
        public Soi(int id, string name, string owner)
        {
            this.ID = id;
            this.Name = name;
            this.OwnerUsername = owner;
            this.StartTime = System.DateTime.Now;
            this.SeatList = new List<Seat>();
            this.SoiOption = new Option();
            this.IsPlaying = false;

            // ván sẽ được khởi tạo sau
        }


        /// <summary>
        /// hệ thống tạo ván mới, tự chia bài, tự xếp chỗ nếu truyền tham số
        /// </summary>
        /// <param name="isXepChoRequired">truyền true để xếp lại random chỗ</param>
        /// <returns>đối tượng ván vừa tạo</returns>
        internal Van CreateVan(bool isXepChoRequired)
        {
            int newVanIndex = 1;
            /// index van moi = index van cu + 1
            if (this._CurrentVan != null)
            {
                newVanIndex = this._CurrentVan.ID++;
            }

            Van oldVan = this._CurrentVan;
            Van newVan = new Van(newVanIndex, this);
            this._CurrentVan = newVan;

            /// xep cho randomly
            if (isXepChoRequired)
            {
                this.XepChoRandom();
            }

            /// Chia bài, chia cho người thắng ván cũ trước (nếu có)
            newVan.ChiaBai(oldVan == null || oldVan.Winner == null ? "" : oldVan.Winner.Username);

            /// reset HaIndex
            Seat tmpSeat = this.SeatList[newVan.CurrentTurnSeatIndex];
            for (int i = 0; i < this.SeatList.Count; i++)
            {
                tmpSeat.HaIndex = i;
                int nextIndex = GetNextSeatIndex(tmpSeat.Pos);
                tmpSeat = this.SeatList[nextIndex];
            }

            return newVan;
        }





        #region Thêm bớt player

        /// <summary>
        /// Tạo một Seat mới trong sới, Ấn user vào seat, tự động đặt owner
        /// </summary>
        /// <param name="player"></param>
        /// <returns> -4 sới đang chơi rồi, -3 đã join sới khác rồi, -2 nếu lỗi, player không tồn tại. -1 nếu sới đã đầy chỗ ,. Trả về số >= 0 nếu OK, hoặc đã join sới rồi cũng là OK</returns>        
        public int AddPlayer(TalaUser player)
        {
            if (player == null)
            {
                // không tìm được online user tương ứng
                return -2;
            }

            if (player.CurrentSoi != null && player.CurrentSoi != this)
            {
                // vào sới khác rồi còn lớ xớ ở đây làm gì
                return -3;
            }

            int nRet = 0;    // function return value
            Seat seatDangNgoiTrongSoi = this.GetSeatByUsername(player.Username);
            if (seatDangNgoiTrongSoi == null)
            {
                if (this.IsPlaying)
                {
                    /// sới đang chơi, chú lại ko phải thằng đang chơi rồi bỏ đi hoặc bị đứt kết nối, 
                    /// mời chú chim cút
                    return -4;
                }

                int count = this.SeatList.Count;
                if (count >= 4)
                {
                    // sới đầy rồi, không cho vào
                    return -1;
                }
                
                // chưa ngồi thì cho vào ngồi
                Seat newSeat = new Seat(-1, player);
                this.SeatList.Add(newSeat);
                this.ReIndexSeatList();
                player.CurrentSoi = this;  // this sới

                if (this.SeatList.Count == 1)
                {
                    // người đầu tiên vào
                    _OwnerUsername = player.Username;
                }

                nRet = newSeat.Pos;
            }
            else
            {
                /// bind lại hai liên kết này, vì có thể khi rơi vào case này, là người này đã thoát khỏi sới, trong seatlist vẫn giữ tên,
                /// nhưng player.CurrentSoi đã bị set rỗng từ trước
                seatDangNgoiTrongSoi.Player = player;
                seatDangNgoiTrongSoi.IsDisconnected = false;
                seatDangNgoiTrongSoi.IsQuitted = false;
                player.CurrentSoi = this;

                // ngồi rồi thì trả ra index chỗ đang ngồi
                nRet = seatDangNgoiTrongSoi.Pos;
            }


            // nếu thêm người chơi thành công, bắt đầu tiến trình autorun
            if (nRet >= 0)
            {
                AutorunService.Create_Autorun_InStartingVan(player);
            }

            if (this.GetCurrentTournament().type != (int)TournamentType.Free)
            {
                // nếu không phải sới free, mask username đi
                player.UsernameInGame = "ThieuGia_" + player.Authkey;
            }

            return nRet;
        }

        /// <summary>
        /// Tạo một Seat mới trong sới, Ấn user vào seat, tự động đặt owner
        /// </summary>
        /// <param name="username"></param>
        /// <returns> -3 đã join sới khác rồi, -2 nếu lỗi, player không tồn tại. -1 nếu sới đã đầy chỗ ,. Trả về số >= 0 nếu OK, hoặc đã join sới rồi cũng là OK</returns>
        public int AddPlayer(string username)
        {
            TalaUser player = Song.Instance.GetUserByUsername(username);
            return AddPlayer(player);
        }


        /// <summary>
        /// Bỏ user ra khỏi danh sách người chơi hiện tại, gỡ bỏ chỗ ngồi
        /// </summary>
        /// <param name="player"></param>
        /// <returns>-2 nếu không đuổi cổ thành công (vì lý do nào đó, player rỗng ...), -1 nếu user vốn không nằm trong sới, >=0 nếu đuổi cổ thành công</returns>
        public int RemovePlayer(TalaUser player)
        {
            // không tìm thấy user này đang online
            if (player == null)
            {
                return -2;
            }

            // sới chả còn ai            
            if (this.SeatList.Count <= 0)
            {
                return -1;
            }

            Seat seatDangNgoi = GetSeatByUsername(player.Username);
            if (seatDangNgoi == null)
            {
                // không tìm thấy user này đang ngồi trong sới
                return -1;
            }
            else
            {
                player.CurrentSoi = null;
                seatDangNgoi.IsQuitted = true;

                if (this.IsPlaying == true
                    && this.CurrentVan != null && this.CurrentVan.IsFinished == false)
                {
                    // để tên nó trong sới, cho chơi nốt, ko bỏ ra khỏi SeatList, chỉ đánh dấu là đã quit thôi

                    // nếu tất cả user đều quit, huỷ sới
                    if (this.SeatList.All(seat => seat.IsQuitted))
                    {
                        // huỷ luôn, ai bảo ngu bỏ hết đi không chơi nữa, mất tiền kệ các chú
                        Song.Instance.DeleteSoi(this.ID.ToString());
                    }
                }
                else
                {
                    // đưa ra khỏi sới, sới chưa chơi, đuổi ngay, cho chim cút
                    this.SeatList.Remove(seatDangNgoi);
                    this.ReIndexSeatList();

                    if (this.SeatList.Count == 0)
                    {
                        // không còn ai, xoá tên owner
                        _OwnerUsername = string.Empty;

                        // bỏ luôn, tránh sới rác
                        // Song.Instance.DeleteSoi(this.ID.ToString());
                    }
                    else if (this.OwnerUsername == player.Username)
                    {
                        // owner rời sới, chuyển owner cho người tiếp theo
                        _OwnerUsername = this.SeatList.First().Player.Username;
                    }
                }

                return 0;
            }
        }

        /// <summary>
        /// Bỏ user ra khỏi danh sách người chơi trong sới hiện tại
        /// </summary>
        /// <param name="username"></param>
        /// <returns>-2 nếu không đuổi cổ thành công (vì lý do nào đó, player rỗng ...), -1 nếu user vốn không nằm trong sới, >=0 nếu đuổi cổ thành công</returns>
        public int RemovePlayer(string username)
        {
            TalaUser player = Song.Instance.GetUserByUsername(username);
            return RemovePlayer(player);
        }


        #endregion




        /// <summary>
        /// Lặp qua các Seat trong sới, nếu username đang ở trong sới, trả về true
        /// </summary>
        /// <param name="username"></param>
        /// <returns></returns>
        public bool IsUserInSoi(string username)
        {
            if (GetSeatByUsername(username) != null)
            {
                return true;
            }
            else
            {
                return false;
            }
        }

        /// <summary>
        /// Lặp qua các Seat trong sới, nếu Player của Seat đó có username trùng với đối số thì trả ra Seat đó, nếu không thì trả về null.
        /// Chú ý, trong trường hợp User đã rời sới nhưng lại rời khi sới đang chơi, tên họ vẫn ở trong sới này, và hàm này vẫn trả ra Seat tương ứng (thực chất seat này do bot ngồi)
        /// </summary>
        /// <param name="username"></param>
        /// <returns></returns>
        public Seat GetSeatByUsername(string username)
        {
            username = username.ToStringSafetyNormalize();
            foreach (Seat seat in this.SeatList)
            {
                if (seat.Player.Username == username)
                {
                    return seat;
                }
            }
            return null;
        }


        /// <summary>
        /// Lấy chỉ số của seat trước so với chỉ số hiện tại của seat. Chỉ số có thể là index hoặc haIndex của Seat
        /// </summary>
        /// <param name="currIndex">index hoặc haIndex của Seat</param>
        /// <param name="seatCount">tổng số seat trong sới</param>
        /// <returns>chỉ số của seat trước</returns>
        public int GetPreviousSeatIndex(int currIndex)
        {
            if (currIndex == 0)
            {
                return this.SeatList.Count - 1;
            }
            return currIndex - 1;
        }


        /// <summary>
        /// Lấy chỉ số của seat sau so với chỉ số hiện tại của seat. Chỉ số có thể là index hoặc haIndex của Seat
        /// </summary>
        /// <param name="currIndex">index hoặc haIndex của Seat</param>
        /// <param name="seatCount">tổng số seat trong sới</param>
        /// <returns>chỉ số của seat sau</returns>
        public int GetNextSeatIndex(int currIndex)
        {
            if (currIndex == this.SeatList.Count - 1)
            {
                return 0;
            }
            return currIndex + 1;
        }


        /// <summary>
        /// Lấy Seat đang có lượt
        /// </summary>
        /// <returns></returns>
        public Seat GetSeatOfCurrentInTurn()
        {
            Seat seatRet = null;
            if (null != this._CurrentVan && this.IsPlaying)
            {
                int nCurrentIndex = this._CurrentVan.CurrentTurnSeatIndex;
                if (0 <= nCurrentIndex && nCurrentIndex < this._SeatList.Count)
                {
                    seatRet = this._SeatList[nCurrentIndex];
                }                
            }
            
            return seatRet;
        }

        /// <summary>
        /// Tìm seat theo hạIndex 
        /// </summary>
        /// <param name="haIndex"></param>
        /// <returns>seat hoặc null nếu không tìm thấy</returns>
        public Seat GetSeatByHaIndex(int haIndex)
        {
            foreach (Seat seat in this.SeatList)
            {
                if (seat.HaIndex == haIndex)
                {
                    return seat;
                }
            }
            return null;
        }

        public tournamentDTO GetCurrentTournament()
        {
            return Song.Instance.GetTournamentByID(this.DBEntry.tournamentid.ToString());
        }

        /// <summary>
        /// Xếp lại chỗ Random các vị trí trong Sới
        /// </summary>
        public void XepChoRandom()
        {
            int max = this.SeatList.Count;
            if (this.SeatList == null || max == 0)
            {
                return;
            }

            /// generate a temp Arr of max elements
            int[] tmpArr = new int[max];
            for (int i = 0; i < max; i++)
            {
                tmpArr[i] = i;
            }

            /// randomly reindexing tmpArr
            int[] randomArr = FunctionExtension.ReindexArrayRandomly(tmpArr);


            /*create new player list*/
            TalaUser[] newPlayerArr = new TalaUser[max];
            /*pick player from SeatList according to randomArr and add to newPlayerArr*/
            for (int i = 0; i < max; i++)
            {
                newPlayerArr[i] = (this.SeatList.ElementAt(randomArr[i]) as Seat).Player;
            }

            /*xep lai SeatList theo newPlayerArr*/
            for (int i = 0; i < max; i++)
            {
                Seat seat = new Seat(i, newPlayerArr[i]);
                this.SeatList.Insert(i, seat);
            }
        }


        /// <summary>
        /// Nếu tất cả sẵn sàng, sới chưa bắt đầu, số người chơi đầy đủ hợp lệ, create ra ván mới, cho anh em chơi
        /// </summary>
        /// <returns>Trả về 1 nếu OK.
        /// Trả về -1 nếu mọi người chưa sẵn sàng, -2 nếu sới đang chơi, -3 số người chơi không hợp lệ (< 2 hoặc  >4)
        /// trả về -4 nếu tourtype = deathmatch, mà lại có chú thiếu tiền không nộp lệ phí đủ được
        /// trả về -5 nếu tournament đã hết hạn chơi</returns>
        public int StartPlaying()
        {
            if (IsPlaying)
            {
                return -2;
            }

            if (IsAllPlayerReady() == false)
            {
                //o	Nếu có player nào chưa ready, lỗi, id=PLAYER_NOT_READY, trong info sẽ có username chưa ready đó
                return -1;
            }

            // ít hơn 2 chú thì ko chơi đc, hơn 4 chú cũng không chơi đc
            if (this.SeatList.Count < 2 || this.SeatList.Count > CONST.MAX_SEAT_IN_SOI_ALLOW)
            {
                return -3;
            }

            // đã hết thời hạn cho phép chơi của tour này
            if(((DateTime)this.GetCurrentTournament().endtime).CompareTo(DateTime.Now) <= 0)
            {
                return -5;
            }

            #region Trừ tiền các đồng chí tham gia, nếu cần (DeathMatch)

            if (this.GetCurrentTournament().type == (int)TournamentType.DeadMatch)
            {
                List<TalaUser> arrUserSubtract = new List<TalaUser>();
                foreach (Seat seat in SeatList)
                {
                    arrUserSubtract.Add(seat.Player);
                }

                IDeathmatchService deathmatchsvc = ServiceLocator.Locate<IDeathmatchService, DeathmatchService>();
                List<string> arrResult = deathmatchsvc.SubtractVCoinBeforeStartSoi(arrUserSubtract, this.GetCurrentTournament());
                if (arrResult.Count <= 0)
                {
                    // không có chú nào thiếu tiền cả, tiền thì trừ rồi, cho chơi thôi                    
                }
                else
                {
                    return -4;
                }
            }

            #endregion

            /* ---------------------------------------------------------------*/
            // COME HERE MEAN all Condition is OK           

            //o	Bắt đầu ván với các lựa chọn của Sới hiện tại
            //o	Hệ thống sẽ tạo ván mới, tự chia bài
            CreateVan(false);

            // bật cờ đang chơi
            IsPlaying = true;
            IsLocked = true;
            this.DBEntry.isend = false;
            this.DBEntry.starttime = new MySqlDateTime(DateTime.Now);
            this.DBEntry.option = this.SoiOption.ToXMLString();


            // tạo countdown timer cho đồng chí có lượt đầu tiên
            AutorunService.Create_Autorun_InVan(this.GetSeatOfCurrentInTurn().Player);

            return 0;
        }

        

               
        /// <summary>
        /// đặt cờ ready tại Seat của user
        /// </summary>
        /// <param name="user">user cần giương cờ ready</param>
        /// <returns>Seat của user đó nếu Seat đã có cờ ready OK, nếu fail thì trả về null</returns>
        public Seat SetReady(TalaUser user)
        {
            // đặt cờ ready
            Seat seatOfUser = GetSeatByUsername(user.Username);
            if (null != seatOfUser)
            {
                seatOfUser.IsReady = true;
            }           

            return seatOfUser;
        }

        /// <summary>
        /// Quét qua các Seat, xem toàn bộ người chơi trong ván đã ready chưa? Nếu trả về true, có thể gọi StartPlaying được
        /// </summary>
        /// <returns></returns>
        public bool IsAllPlayerReady()
        {
            bool bAllPlayerReady = true;
            foreach (Seat seat in SeatList)
            {
                bAllPlayerReady = bAllPlayerReady && seat.IsReady;
            }
            return bAllPlayerReady;
        }


        /// <summary>
        /// sắp xếp lại index của các chỗ ngồi trong mảng SeatList. 
        /// Hàm này cần phải gọi mỗi khi có sự thay đổi vè chỗ ngồi trong  Sới (thêm bớt player)
        /// Hàm sẽ gán Pos của mỗi Seat = chính index của Seat đó trong SeatList của sới
        /// </summary>
        internal void ReIndexSeatList()
        {
            foreach (Seat seat in SeatList)
            {
                seat.Pos = SeatList.IndexOf(seat);
                seat.HaIndex = seat.Pos;
            }
        }

        public string Autorun()
        {   
            if(this.IsPlaying)
            {
                return AutorunService.Check_Autorun_InVan(this);
            }
            else
            {
                return AutorunService.Check_Autorun_InStartingVan(this);                
            }
        }

        #region temporary code for load testing

        /// <summary>
        /// for testing only
        /// Create van mới, chia bài theo 1 cách xác định 
        /// </summary>
        /// <returns></returns>
        public int StartPlayingForTesting()
        {
            if (IsPlaying)
            {
                return -2;
            }

            if (IsAllPlayerReady() == false)
            {
                //o	Nếu có player nào chưa ready, lỗi, id=PLAYER_NOT_READY, trong info sẽ có username chưa ready đó
                return -1;
            }


            if (4 < this.SeatList.Count || this.SeatList.Count < 2)
            {
                return -3;
            }



            // COME HERE MEAN all Condition is OK

            // bật cờ đang chơi
            IsPlaying = true;
            //o	Bắt đầu ván với các lựa chọn của Sới hiện tại
            //o	Hệ thống sẽ tạo ván mới, tự chia bài

            CreateVanForTesting(false);
            return 0;
        }

        /// <summary>
        /// hệ thống tạo ván mới, tự chia bài, tự xếp chỗ nếu truyền tham số
        /// </summary>
        /// <param name="isXepChoRequired">truyền true để xếp lại random chỗ</param>
        /// <returns>đối tượng ván vừa tạo</returns>
        internal Van CreateVanForTesting(bool isXepChoRequired)
        {
            int newVanIndex = 1;
            /// index van moi = index van cu + 1
            if (this._CurrentVan != null)
            {
                newVanIndex = this._CurrentVan.ID++;
            }

            Van oldVan = this._CurrentVan;
            Van newVan = new Van(newVanIndex, this, true);
            this._CurrentVan = newVan;

            /// xep cho randomly
            if (isXepChoRequired)
            {
                this.XepChoRandom();
            }

            /// Chia bài, chia cho người thắng ván cũ trước (nếu có)
            newVan.ChiaBai(oldVan == null ? "" : oldVan.Winner.Username);

            /// reset HaIndex
            Seat tmpSeat = this.SeatList[newVan.CurrentTurnSeatIndex];
            for (int i = 0; i < this.SeatList.Count; i++)
            {
                tmpSeat.HaIndex = i;
                int nextIndex = GetNextSeatIndex(tmpSeat.Pos);
                tmpSeat = this.SeatList[nextIndex];
            }

            return newVan;
        }
        #endregion
    }
}
