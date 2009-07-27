using System;
using System.Collections;
using System.Xml.Serialization;

using GURUCORE.Framework.Core.Data;

namespace Quantum.Tala.Service.DTO
{
	/// <summary>
	/// Summary for user_statDTO.
	/// </summary>
	[Serializable]
	[System.Diagnostics.DebuggerStepThrough]
	[PersistenceClass("playing_user_stat","id",true)]
	 public sealed partial class user_statDTO : DTOBase
	{
		public const string ID_FLD = "id";
		public const string U_FLD = "u";
		public const string WIN_FLD = "win";
		public const string LOSE_FLD = "lose";
		public const string POINT_FLD = "point";
		public const string DT_FLD = "dt";
		

		private int m_nid;
		private string m_su;
		private int m_nwin;
		private int m_nlose;
		private int m_npoint;
		private object m_odt;
		

		public user_statDTO()
		{
		}
		
		
		
		[PersistenceProperty("id",true,true)]
		public int id
		{
			get
			{
				return m_nid;
			}
			set
			{
				m_htIsNull["id"] = false;
				m_nid = value;
			}
		}
		
		[PersistenceProperty("u",false,false)]
		public string u
		{
			get
			{
				return m_su;
			}
			set
			{
				m_htIsNull["u"] = false;
				m_su = value;
			}
		}
		
		[PersistenceProperty("win",false,false)]
		public int win
		{
			get
			{
				return m_nwin;
			}
			set
			{
				m_htIsNull["win"] = false;
				m_nwin = value;
			}
		}
		
		[PersistenceProperty("lose",false,false)]
		public int lose
		{
			get
			{
				return m_nlose;
			}
			set
			{
				m_htIsNull["lose"] = false;
				m_nlose = value;
			}
		}
		
		[PersistenceProperty("point",false,false)]
		public int point
		{
			get
			{
				return m_npoint;
			}
			set
			{
				m_htIsNull["point"] = false;
				m_npoint = value;
			}
		}
		
		[PersistenceProperty("dt",false,false)]
		public object dt
		{
			get
			{
				return m_odt;
			}
			set
			{
				m_htIsNull["dt"] = false;
				m_odt = value;
			}
		}

		public string IsNullSerializedString
		{
			get
			{
				return GetIsNullSerializedString();
			}
			set
			{
				SetIsNullSerializedString(value);
			}
		}
	}
}
